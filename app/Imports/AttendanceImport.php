<?php

namespace App\Imports;

use App\Models\AttendanceRecord;
use App\Models\LeaderEmployee; // Import the LeaderEmployee model
use App\Models\User;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Exception;
use Carbon\Carbon;

class AttendanceImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $collection->shift(); // Skip header row

        $errors = []; // Array to store validation errors
        $validatedData = []; // Array to store validated data

        // Get the Authenticated User
        $leader = Auth::user();
        if (!$leader) {
            throw new Exception("Unauthorized access: You must be logged in.");
        }
        $leaderId = $leader->id;
        $leaderRole = $leader->role; // Get user role

        foreach ($collection as $index => $row) {
            $lineNumber = $index + 2; // Consider header and zero-based index
            try {
                // Validate username
                $username = $row[0];
                $user = User::where('username', $username)->first();

                if (!$user) {
                    throw new Exception("User not found for username '{$username}' at line {$lineNumber}.");
                }
                $userId = $user->id;

                // Check if user has permission to update this schedule
                if ($leaderRole !== "1") { // If not admin (role = 1)
                    $isAssignedEmployee = LeaderEmployee::where('Leader_id', $leaderId)
                        ->where('employee_id', $userId)
                        ->exists();

                    if (!$isAssignedEmployee) {
                        throw new Exception("Unauthorized: The username '{$username}' at line {$lineNumber} does not belong to your team.");
                    }
                }

                // Validate and convert date/time
                try {
                    $shiftIn = Carbon::instance(Date::excelToDateTimeObject($row[1] + $row[2]));
                    $shiftOut = Carbon::instance(Date::excelToDateTimeObject($row[3] + $row[4]));
                } catch (Exception $e) {
                    throw new Exception("Invalid date/time format at line {$lineNumber}.");
                }

                // Ensure shiftOut is not before shiftIn
                if ($shiftOut->lessThan($shiftIn)) {
                    throw new Exception("Shift out time is earlier than shift in time at line {$lineNumber}.");
                }

                // Ensure duty hours do not exceed 9 hours
                $dutyHours = $shiftIn->diffInMinutes($shiftOut);
                if ($dutyHours > 540) { // 9 hours * 60 minutes
                    throw new Exception("Duty hours exceed the allowed 9-hour limit at line {$lineNumber}.");
                }

                // Determine dayoff value
                $dayoff = strtolower(trim($row[5])) === 'off' ? 'yes' : 'no';

                // Add validated data to the array
                $validatedData[] = [
                    'user_id' => $userId,
                    'leader_id' => $leaderId,
                    'shift_in' => $shiftIn->format('Y-m-d H:i:s'),
                    'shift_out' => $shiftOut->format('Y-m-d H:i:s'),
                    'dayoff' => $dayoff,
                ];
            } catch (Exception $e) {
                // Capture error with line number
                $errors[] = $e->getMessage();
            }
        }

        // If errors exist, throw an exception with the error details in a readable format
        if (!empty($errors)) {
            throw new Exception(implode(' | ', $errors));
        }

        // If no errors, insert all validated data
        foreach ($validatedData as $data) {
            AttendanceRecord::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'leader_id' => $data['leader_id'],
                    'shift_in' => $data['shift_in'],
                    'shift_out' => $data['shift_out'],
                ],
                [
                    'dayoff' => $data['dayoff'],
                ]
            );
        }
    }
}
