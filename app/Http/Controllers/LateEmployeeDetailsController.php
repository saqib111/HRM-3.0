<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

use App\Models\{
    User,
    LeaderEmployee,
    Group,
    AttendanceSession,
    FingerPrint,
    ApprovedLeave,
    AttendanceRecord
};

class LateEmployeeDetailsController extends Controller
{
    public function lateEmployees()
    {
        return view("attendance.late-employee-details");
    }

    public function lateEmployeeRecord()
    {
        $id = auth()->user()->id;

        // Get all the team members of the leader
        $team_data = LeaderEmployee::where("leader_id", $id)->get();
        $team_members_id = $team_data->pluck("employee_id");

        // Get the numeric part of employee_id from the Users table
        $verify_ids = User::whereIn('id', $team_members_id)->get()
            ->mapWithKeys(function ($user) {
                $numeric_id = preg_replace('/\D/', '', $user->employee_id);
                return [$user->id => $numeric_id]; // Map user_id => numeric_id
            });

        $start_of_month = Carbon::now()->startOfMonth();
        $yesterday = Carbon::yesterday()->endOfDay();

        // GETTING THE APPROVED LEAVES DATA
        $approved_leaves = ApprovedLeave::whereIn("user_id", $team_members_id)
            ->whereBetween("date", [$start_of_month, $yesterday])
            ->get(); // Get the full collection for easy matching later

        // Now we create an array of `user_id` and leave dates
        $leave_dates = [];
        foreach ($approved_leaves as $leave) {
            $leave_dates[$leave->user_id][] = $leave->date;
        }

        // Fetch attendance records for the team
        $active_user_role = auth()->user()->role;

        $team_attendance_record = AttendanceRecord::query();

        if ($active_user_role === "1") {
            // If the user is a team leader (role "1"), return all attendance records.
            $team_attendance_record = $team_attendance_record
                ->whereBetween("shift_in", [$start_of_month, $yesterday])
                ->where("dayoff", "no")
                ->whereNotNull('shift_in')
                ->whereNotNull('shift_out')
                ->where(function ($query) {
                    $query->whereNull("check_in")
                        ->whereNull("check_out")
                        ->orWhereRaw("check_in > shift_in")
                        ->orWhereRaw("check_out < shift_out")
                        ->orWhereRaw("DATE(check_out) > DATE(shift_out)");
                })
                ->whereNotIn(
                    DB::raw('CONCAT(user_id, "_", DATE(shift_in))'),
                    // Exclude records where user_id and date are in the approved leave dates
                    collect($leave_dates)->flatMap(function ($dates, $user_id) {
                        return collect($dates)->map(function ($date) use ($user_id) {
                            return $user_id . "_" . $date;
                        });
                    })->toArray()
                );
        } else {
            // If the user is not a team leader, filter based on team members
            $team_attendance_record = $team_attendance_record
                ->whereIn("user_id", $team_members_id)
                ->whereBetween("shift_in", [$start_of_month, $yesterday])
                ->where("dayoff", "no")
                ->whereNotNull('shift_in')
                ->whereNotNull('shift_out')
                ->where(function ($query) {
                    $query->whereNull("check_in")
                        ->whereNull("check_out")
                        ->orWhereRaw("check_in > shift_in")
                        ->orWhereRaw("check_out < shift_out")
                        ->orWhereRaw("DATE(check_out) > DATE(shift_out)");
                })
                ->whereNotIn(
                    DB::raw('CONCAT(user_id, "_", DATE(shift_in))'),
                    // Exclude records where user_id and date are in the approved leave dates
                    collect($leave_dates)->flatMap(function ($dates, $user_id) {
                        return collect($dates)->map(function ($date) use ($user_id) {
                            return $user_id . "_" . $date;
                        });
                    })->toArray()
                );
        }

        $team_attendance_record = $team_attendance_record
            ->with('user')
            ->get();

        // Process each attendance record
        foreach ($team_attendance_record as $record) {
            $shift_in = Carbon::parse($record->shift_in);
            $shift_out = Carbon::parse($record->shift_out);
            $check_in = !is_null($record->check_in) ? Carbon::parse($record->check_in) : null;
            $check_out = !is_null($record->check_out) ? Carbon::parse($record->check_out) : null;

            $numeric_id = $verify_ids[$record->user_id] ?? null;

            if ($numeric_id) {
                // Fetch fingerprint logs for the numeric_id on the given shift_in date
                $fingerprints = FingerPrint::where('user_id', $numeric_id)
                    ->whereDate('fingerprint_in', $shift_in->toDateString())
                    ->get();

                if ($fingerprints->isEmpty()) {
                    // No fingerprint data available for the date
                    $record->fingerprint_checkin_status = "no_data_fingerprint";
                    $record->fingerprint_checkout_status = "no_data_fingerprint";
                } else {
                    // Check for fingerprints of type = 0 (check-in)
                    $check_in_fingerprints = $fingerprints->where('type', 0);
                    if ($check_in_fingerprints->isEmpty()) {
                        $record->fingerprint_checkin_status = "no_data_fingerprint";
                    } else {
                        // Match fingerprints within 10 minutes
                        $matching_check_in = $check_in_fingerprints->filter(function ($fp) use ($check_in) {
                            return $check_in && abs(Carbon::parse($fp->fingerprint_in)->diffInMinutes($check_in)) <= 10;
                        })->first();

                        $record->fingerprint_checkin_status = $matching_check_in
                            ? "yes_record_found"
                            : "no_late_record_found";
                    }

                    // Check for fingerprints of type = 1 (check-out)
                    $check_out_fingerprints = $fingerprints->where('type', 1);
                    if ($check_out_fingerprints->isEmpty()) {
                        $record->fingerprint_checkout_status = "no_data_fingerprint";
                    } else {
                        // Match fingerprints within 10 minutes
                        $matching_check_out = $check_out_fingerprints->filter(function ($fp) use ($check_out) {
                            return $check_out && abs(Carbon::parse($fp->fingerprint_in)->diffInMinutes($check_out)) <= 10;
                        })->first();

                        $record->fingerprint_checkout_status = $matching_check_out
                            ? "yes_record_found"
                            : "no_late_record_found";
                    }
                }
            } else {
                $record->fingerprint_checkin_status = "no_data_fingerprint";
                $record->fingerprint_checkout_status = "no_data_fingerprint";
            }

            // Add check-in and check-out status
            $record->check_in_status = $check_in
                ? ($check_in > $shift_in ? 'Late' : 'On Time')
                : 'Absent';

            // Updated Check-Out Status Logic
            if (is_null($check_out)) {
                $record->check_out_status = 'Not Checked Out';
            } elseif ($check_out->gt($shift_out) && !$check_out->isSameDay($shift_out)) {
                $record->check_out_status = 'Extended'; // Check-Out is on the next day
            } elseif ($check_out->lt($shift_out)) {
                $record->check_out_status = 'Early';
            } else {
                $record->check_out_status = 'On Time';
            }

            // Duty Hours Calculation (Restored Logic)
            if ($check_in && $check_out) {
                // Calculate effective check-in and check-out within shift boundaries
                $effective_check_in = $check_in->lt($shift_in) ? $shift_in : $check_in; // Use shift_in if check_in is earlier
                $effective_check_out = $check_out->gt($shift_out) ? $shift_out : $check_out; // Use shift_out if check_out is later

                // Calculate duty minutes based on effective times
                $duty_minutes = $effective_check_in->diffInMinutes($effective_check_out);
            } elseif (is_null($check_in) && is_null($check_out)) {
                // Case when both check_in and check_out are null
                $duty_minutes = 0;
            } else {
                // If either check_in or check_out is missing, calculate from shift_in to shift_out
                $duty_minutes = $shift_in->diffInMinutes($shift_out);
            }

            // Convert duty minutes to H:M:S format
            $hours = floor($duty_minutes / 60);
            $minutes = $duty_minutes % 60;
            $seconds = ($duty_minutes * 60) % 60;

            // Format time as H:M:S
            $formatted_time = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

            // Assign duty hours
            $record->duty_hours = $formatted_time;
        }

        // Return the data as a DataTable
        return DataTables::of($team_attendance_record)
            ->addIndexColumn()
            ->addColumn('username', function ($row) {
                return $row->user ? $row->user->username : 'N/A';
            })
            ->addColumn('check_in_status', function ($row) {
                return $row->check_in_status ?? "N/A";
            })
            ->addColumn('check_out_status', function ($row) {
                return $row->check_out_status ?? "N/A";
            })
            ->addColumn('fingerprint_checkin_status', function ($row) {
                return $row->fingerprint_checkin_status ?? "N/A";
            })
            ->addColumn('fingerprint_checkout_status', function ($row) {
                return $row->fingerprint_checkout_status ?? "N/A";
            })
            ->addColumn("duty_hours", function ($row) {
                return $row->duty_hours ?? "N/A";
            })
            ->make(true);
    }
}