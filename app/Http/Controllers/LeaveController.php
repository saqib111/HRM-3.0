<?php

namespace App\Http\Controllers;

use App\Models\AnnualLeaves;
use Illuminate\Http\Request;
use App\Models\LeaveManagement;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function leave_form()
    {
        $active_user_id = auth()->user()->id;
        $annualLeaves = AnnualLeaves::where('user_id', '=', $active_user_id)->first();
        $annualLeaveBalance = $annualLeaves->leave_balance;
        // Format the leave balance to remove unnecessary trailing zeros
        $formattedLeaveBalance = rtrim(rtrim($annualLeaveBalance, '0'), '.');
        return view('leave_application.leave_form', compact('formattedLeaveBalance'));
    }

    public function store_leave(Request $request)
    {
        // Step 1: Validate the request using Validator for AJAX
        $validator = Validator::make($request->all(), [
            'leave_title' => 'required|string|max:255',
            'annual_leave_balance' => 'required|numeric',
            'full_day_leave.*' => 'nullable|integer', // assuming you pass leave types as integers
            'full_leave_from.*' => 'nullable|date',
            'full_leave_to.*' => 'nullable|date|after_or_equal:full_leave_from.*',
            'half_day_date.*' => 'nullable|date',
            'half_day_start_time.*' => 'nullable|date_format:H:i',
            'half_day_end_time.*' => 'nullable|date_format:H:i|after:half_day_start_time.*',
            'off_days.*' => 'nullable|date',
        ]);

        // Step 2: Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Step 3: Check if the user has already taken a Birthday Leave this year
        $user_id = auth()->user()->id; // Assuming the user is authenticated
        $errors = [];

        // Check if "Birthday Leave" is selected in the request
        if (in_array(2, $request->full_day_leave ?? [])) { // 2 stands for "Birthday Leave"
            $currentYear = now()->year;
            $birthdayLeaveTaken = LeaveManagement::where('user_id', $user_id)
                ->whereJsonContains('leave_details', [['leave_type_id' => "2"]])
                ->whereYear('created_at', $currentYear)
                ->exists();

            if ($birthdayLeaveTaken) {
                $errors['birthday_leave'] = 'You have already taken Birthday Leave this year.';
            }
        }

        // Check total "Marriage Leave" days taken in lifetime
        if (in_array(3, $request->full_day_leave ?? [])) { // 3 stands for "Marriage Leave"
            $marriageLeaveDaysTaken = LeaveManagement::where('user_id', $user_id)
                ->whereJsonContains('leave_details', [['leave_type_id' => "3"]])
                ->get()
                ->sum(function ($leave) {
                    $details = json_decode($leave->leave_details, true);
                    return collect($details)->where('leave_type_id', "3")->sum(function ($marriageLeave) {
                        $start = new \DateTime($marriageLeave['start_date']);
                        $end = new \DateTime($marriageLeave['end_date']);
                        return $end->diff($start)->days + 1;
                    });
                });

            // Calculate new requested Marriage Leave days
            $newMarriageLeaveDaysRequested = 0;
            if (!empty($request->full_day_leave)) {
                foreach ($request->full_day_leave as $index => $leave_type) {
                    if ($leave_type == "3") {
                        $from = new \DateTime($request->full_leave_from[$index]);
                        $to = new \DateTime($request->full_leave_to[$index]);
                        $newMarriageLeaveDaysRequested += $to->diff($from)->days + 1;
                    }
                }
            }

            if ($marriageLeaveDaysTaken + $newMarriageLeaveDaysRequested > 3) {
                $errors['marriage_leave'] = 'Marriage Leave cannot exceed a total of 3 days in your lifetime.';
            }
        }

        // Return errors if any exist
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        // Step 4: Get form inputs
        $leave_title = $request->leave_title;
        $description = $request->description;
        $user_id = auth()->user()->id; // Assuming the user is authenticated
        $annual_leave_balance = $request->annual_leave_balance;
        $off_days = $request->off_days ?? [];

        // Step 5: Prepare leave details array
        $leave_details = [];

        // Full-day leave processing
        if (!empty($request->full_day_leave)) {
            foreach ($request->full_day_leave as $index => $leave_type) {
                $from = $request->full_leave_from[$index];
                $to = $request->full_leave_to[$index];

                // Calculate the effective leave days (excluding off-days)
                $total_days = $this->calculateLeaveDays($from, $to, $off_days);

                if ($leave_type == 1 && $total_days > $annual_leave_balance) { // 1 is for "Annual Leave"
                    // Store the part covered by annual leave
                    $paid_end_date = $this->calculateRemainingUnpaidStartDate($from, $annual_leave_balance, $off_days);

                    $leave_details[] = [
                        'type' => 'full_day',
                        'leave_type_id' => 1, // Annual Leave
                        'start_date' => $from,
                        'end_date' => $paid_end_date,
                        'status' => 'paid'
                    ];

                    // Store the unpaid part starting after paid days end
                    $unpaid_start_date = (new \DateTime($paid_end_date))->modify('+1 day')->format('Y-m-d');
                    $leave_details[] = [
                        'type' => 'full_day',
                        'leave_type_id' => 4, // Unpaid Leave
                        'start_date' => $unpaid_start_date,
                        'end_date' => $to,
                        'status' => 'unpaid'
                    ];
                } else {
                    $leave_details[] = [
                        'type' => 'full_day',
                        'leave_type_id' => $leave_type, // Leave type ID is sent in the form
                        'start_date' => $from,
                        'end_date' => $to,
                        'status' => 'paid'
                    ];
                }
            }
        }

        // Half-day leave processing
        if (!empty($request->half_day_date)) {
            foreach ($request->half_day_date as $index => $half_day_date) {
                $start_time = $request->half_day_start_time[$index];
                $end_time = $request->half_day_end_time[$index];

                $leave_details[] = [
                    'type' => 'half_day',
                    'leave_type_id' => 1, // Assuming it's covered by annual leave
                    'date' => $half_day_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ];
            }
        }

        // Off-days processing
        if (!empty($request->off_days)) {
            foreach ($request->off_days as $off_day) {
                $leave_details[] = [
                    'type' => 'off_day',
                    'date' => $off_day
                ];
            }
        }

        // Step 6: Save leave application to the database
        LeaveManagement::create([
            'user_id' => $user_id,
            'title' => $leave_title,
            'description' => $description,
            'leave_balance' => $annual_leave_balance,
            'leave_details' => json_encode($leave_details), // Convert the array to JSON
            'status_1' => 'pending',
            'status_2' => 'pending',
            'team_leader_ids' => json_encode([2, 3]), // Replace with dynamic data if needed
            'manager_ids' => json_encode([4, 5]), // Replace with dynamic data if needed
            'first_approval_id' => null,
            'first_approval_created_time' => null,
            'second_approval_id' => null,
            'second_approval_created_time' => null,
            'hr_approval_id' => null,
            'hr_approval_created_time' => null
        ]);

        // Step 7: Return success response for AJAX
        return response()->json(['message' => 'Leave application submitted successfully.'], 200);
    }


    /**
     * Calculate the number of leave days excluding the given off days
     */
    // Helper method to calculate leave days excluding off-days
    private function calculateLeaveDays($start, $end, $off_days = [])
    {
        $start_date = new \DateTime($start);
        $end_date = new \DateTime($end);
        $days_count = 0;

        while ($start_date <= $end_date) {
            $current_date_str = $start_date->format('Y-m-d');

            // Only count the day if it's not an off-day
            if (!in_array($current_date_str, $off_days)) {
                $days_count++;
            }

            $start_date->modify('+1 day');
        }

        return $days_count;
    }

    // Helper method to calculate the end date of paid leave (covered by balance)
    private function calculateRemainingUnpaidStartDate($start, $annual_leave_balance, $off_days = [])
    {
        $start_date = new \DateTime($start);
        $days_used = 0;

        // Move forward in days until reaching the full annual leave balance, skipping off-days
        while ($days_used < $annual_leave_balance) {
            $current_date_str = $start_date->format('Y-m-d');

            // Only count the day if it is not an off-day
            if (!in_array($current_date_str, $off_days)) {
                $days_used++;
            }

            if ($days_used < $annual_leave_balance) {
                $start_date->modify('+1 day');
            }
        }

        return $start_date->format('Y-m-d');
    }











































    public function display_leave(Request $request)
    {
        if ($request->ajax()) {
            // $leaves = LeaveManagement::with('user')
            //     ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance']);
            $authId = auth()->user()->id;
            $leaves = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance'])
                ->where(function ($query) use ($authId) {
                    $query->where(function ($subQuery) use ($authId) {
                        // If status_1 is pending
                        $subQuery->where('status_1', 'pending')
                            ->whereJsonContains('team_leader_ids', $authId);
                    })
                        ->orWhere(function ($subQuery) use ($authId) {
                            // If status_1 is approved and status_2 is pending
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'pending')
                                ->whereJsonContains('manager_ids', $authId);
                        });
                })
                ->get();


            return Datatables::of($leaves)
                ->addIndexColumn()
                ->addColumn('username', function ($row) {
                    return $row->user->username ?? 'N/A';
                })
                ->addColumn('employee_id', function ($row) {
                    return $row->user->employee_id ?? 'N/A';
                })
                ->addColumn('day', function ($row) {
                    $details = json_decode($row->leave_details);
                    $day_str = '';

                    foreach ($details as $detail) {
                        if ($detail->type === 'full_day') {
                            $day_str .= '<span class="badge bg-primary">Full Day</span><br>';
                        } elseif ($detail->type === 'half_day') {
                            $day_str .= '<span class="badge bg-warning">Half Day</span><br>';
                        }
                    }
                    return $day_str;
                })
                ->addColumn('from', function ($row) {
                    $details = json_decode($row->leave_details);
                    $from_str = '';

                    foreach ($details as $detail) {
                        if ($detail->type === 'full_day') {
                            $from_str .= $detail->start_date . '<br>';
                        } elseif ($detail->type === 'half_day') {
                            $from_str .= $detail->date . ' (' . $detail->start_time . ')<br>';
                        }
                    }
                    return $from_str;
                })
                ->addColumn('to', function ($row) {
                    $details = json_decode($row->leave_details);
                    $to_str = '';

                    foreach ($details as $detail) {
                        if ($detail->type === 'full_day') {
                            $to_str .= $detail->end_date . '<br>';
                        } elseif ($detail->type === 'half_day') {
                            $to_str .= $detail->date . ' (' . $detail->end_time . ')<br>';
                        }
                    }
                    return $to_str;
                })
                ->addColumn('off_days', function ($row) {
                    $details = json_decode($row->leave_details);
                    $off_day_str = '<ul class="list-unstyled mb-0">';

                    foreach ($details as $detail) {
                        if ($detail->type === 'off_day') {
                            $off_day_str .= "<li><span class='badge bg-secondary'>{$detail->date}</span></li>";
                        }
                    }

                    return $off_day_str .= '</ul>';
                })
                ->rawColumns(['day', 'from', 'to', 'off_days'])
                ->make(true);
        }

        return view('leave_application.pending_leaves');
    }

    // Function for dynamic loading data for modal.
    public function getLeaveApplication($id)
    {
        $leave = LeaveManagement::with('user')->findOrFail($id);
        // Call the helper function to get the username
        $first_approval_username = getUser($leave->first_approval_id);
        $formattedLeaveBalance = rtrim(rtrim($leave->leave_balance, '0'), '.');

        return response()->json([
            'id' => $leave->id,
            'employee_id' => $leave->user->employee_id,
            'username' => $leave->user->username,
            'title' => $leave->title,
            'description' => $leave->description,
            'leave_balance' => $formattedLeaveBalance,
            'leave_details' => json_decode($leave->leave_details), // Ensure leave details are properly decoded
            'status_1' => $leave->status_1,
            // Get User ID
            'first_approval_id' => $first_approval_username ?? "Null",
            'first_approval_created_time' => $leave->first_approval_created_time ?? "YYYY-MM-DD HH:MM:SS",
            'status_2' => $leave->status_2,
            // Get User ID
            'second_approval_id' => $second_approval_username ?? "Null",
            'second_approval_created_time' => $leave->second_approval_created_time ?? "YYYY-MM-DD HH:MM:SS",
        ]);
    }

    // Actions for approval/rejection
    public function leave_action(Request $request)
    {
        // Validate the request
        $request->validate([
            'leave_id' => 'required|integer',
            'leave_action' => 'required|string',
            'leave_step' => 'required|string', // Ensure leave_step is also validated
        ]);

        $leave_id = $request->leave_id;
        $leave_action = $request->leave_action;
        $leave_step = $request->leave_step;
        $activeUserId = auth()->user()->id;


        $tester = "";

        // Perform your action based on the leave action
        if ($leave_action === "approval_request") {
            if ($leave_step === 'first_status') {
                $leaveUpdate = LeaveManagement::find($leave_id);
                $leaveUpdate->status_1 = "approved";
                $leaveUpdate->first_approval_id = $activeUserId;
                $leaveUpdate->first_approval_created_time = now();

                $leaveUpdate->save();

                $tester = "first step approval is done"; // Correct way to concatenate strings in PHP
                // Logic for first step approval
            } elseif ($leave_step === 'second_status') {
                // Fetch the leave application
                $leaveUpdate = LeaveManagement::find($leave_id);
                $leaveUpdate->status_2 = "approved";
                $leaveUpdate->second_approval_id = $activeUserId;
                $leaveUpdate->second_approval_created_time = now();

                // Decode leave_details JSON
                $leaveDetails = json_decode($leaveUpdate->leave_details, true);

                // Get annual leave balance for the user from the annual_leaves table
                $annualLeave = AnnualLeaves::where('user_id', $leaveUpdate->user_id)->first();

                if (!$annualLeave) {
                    return response()->json(['error' => 'Annual leave balance not found.'], 404);
                }

                $totalAnnualLeaveDays = 0;

                foreach ($leaveDetails as $leave) {
                    if (isset($leave['leave_type_id'])) {
                        $leaveTypeId = (int) $leave['leave_type_id'];

                        if ($leave['type'] === 'full_day' && $leaveTypeId === 1) {
                            // Calculate the days between start and end dates for full-day annual leaves
                            $startDate = new \DateTime($leave['start_date']);
                            $endDate = new \DateTime($leave['end_date']);
                            $days = $startDate->diff($endDate)->days + 1;

                            for ($i = 0; $i < $days; $i++) {
                                $currentDate = $startDate->format('Y-m-d');

                                // Check for off days
                                $isOffDay = collect($leaveDetails)->contains(function ($detail) use ($currentDate) {
                                    return $detail['type'] === 'off_day' && $detail['date'] === $currentDate;
                                });

                                if (!$isOffDay) {
                                    // Check if there's enough balance for Annual Leave
                                    if ($annualLeave->leave_balance > 0) {
                                        // Deduct from Annual Leave Balance
                                        DB::table('approved_leaves')->insert([
                                            'user_id' => $leaveUpdate->user_id,
                                            'leave_type' => $leaveTypeId, // Store as integer for 'AL'
                                            'date' => $currentDate,
                                        ]);
                                        $totalAnnualLeaveDays++;
                                        $annualLeave->leave_balance--;
                                    } else {
                                        // If no balance, convert to Unpaid Leave (leave_type 4)
                                        DB::table('approved_leaves')->insert([
                                            'user_id' => $leaveUpdate->user_id,
                                            'leave_type' => 4, // Unpaid Leave
                                            'date' => $currentDate,
                                        ]);
                                    }
                                }

                                $startDate->modify('+1 day');
                            }
                        } elseif ($leave['type'] === 'half_day' && $leaveTypeId === 1) {
                            // Half-day leave for Annual Leave
                            $isOffDay = collect($leaveDetails)->contains(function ($detail) use ($leave) {
                                return $detail['type'] === 'off_day' && $detail['date'] === $leave['date'];
                            });

                            if (!$isOffDay) {
                                // Check if there's enough balance for Annual Leave
                                if ($annualLeave->leave_balance >= 0.5) {
                                    // Deduct 0.5 days for half-day leave
                                    DB::table('approved_leaves')->insert([
                                        'user_id' => $leaveUpdate->user_id,
                                        'leave_type' => $leaveTypeId, // Store as integer for 'AL'
                                        'date' => $leave['date'],
                                        'start_time' => $leave['start_time'],
                                        'end_time' => $leave['end_time'],
                                    ]);
                                    $totalAnnualLeaveDays += 0.5;
                                    $annualLeave->leave_balance -= 0.5;
                                } else {
                                    // If no balance, convert to Unpaid Leave (leave_type 4)
                                    DB::table('approved_leaves')->insert([
                                        'user_id' => $leaveUpdate->user_id,
                                        'leave_type' => 4, // Unpaid Leave
                                        'date' => $leave['date'],
                                        'start_time' => $leave['start_time'],
                                        'end_time' => $leave['end_time'],
                                    ]);
                                }
                            }
                        } elseif ($leave['type'] === 'full_day' && in_array($leaveTypeId, [2, 3, 4])) {
                            // For other full-day leaves that are not Annual Leave (Birthday, Marriage, Unpaid)
                            $startDate = new \DateTime($leave['start_date']);
                            $endDate = new \DateTime($leave['end_date']);
                            $days = $startDate->diff($endDate)->days + 1;

                            for ($i = 0; $i < $days; $i++) {
                                $currentDate = $startDate->format('Y-m-d');

                                // Add this day to the approved_leaves table without deduction
                                DB::table('approved_leaves')->insert([
                                    'user_id' => $leaveUpdate->user_id,
                                    'leave_type' => $leaveTypeId, // Store as integer for type (2 = BL, 3 = ML, 4 = UL)
                                    'date' => $currentDate,
                                ]);

                                $startDate->modify('+1 day');
                            }
                        }
                    }
                }

                // Save the leave approval changes
                $leaveUpdate->save();
                $annualLeave->save(); // Save updated leave balance

                $tester = "second step approval";
                // Logic for second step approval
            }
        } elseif ($leave_action === "reject_request") {
            if ($leave_step === 'first_status') {
                $leaveUpdate = LeaveManagement::find($leave_id);
                $leaveUpdate->status_1 = "rejected";
                $leaveUpdate->first_approval_id = $activeUserId;
                $leaveUpdate->first_approval_created_time = now();

                $leaveUpdate->save();

                $tester = "first step rejection";
                // Logic for first step rejection
            } elseif ($leave_step === 'second_status') {
                $leaveUpdate = LeaveManagement::find($leave_id);
                $leaveUpdate->status_2 = "rejected";
                $leaveUpdate->second_approval_id = $activeUserId;
                $leaveUpdate->second_approval_created_time = now();

                $leaveUpdate->save();
                $tester = "second step rejection";
                // Logic for second step rejection
            }
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'leave_id' => $leave_id,
            'leave_action' => $leave_action,
            'tester' => $tester
        ]);
    }


}
