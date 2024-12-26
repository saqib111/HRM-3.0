<?php

namespace App\Http\Controllers;

use App\Models\AnnualLeaves;
use Illuminate\Http\Request;
use App\Models\LeaveManagement;
use App\Models\User;
use App\Models\AssignedLeaveApprovals;
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
        // Step 1: Validate the request
        $validator = Validator::make($request->all(), [
            'leave_title' => 'required|string|max:255',
            'annual_leave_balance' => 'required|numeric',
            'full_day_leave.*' => 'nullable|integer',
            'full_leave_from.*' => 'nullable|date',
            'full_leave_to.*' => 'nullable|date|after_or_equal:full_leave_from.*',
            'half_day_date.*' => 'nullable|date',
            'half_day_start_time.*' => 'nullable|date_format:H:i',
            'half_day_end_time.*' => 'nullable|date_format:H:i|after:half_day_start_time.*',
            'off_days.*' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Step 2: Check if the user has already taken a Birthday Leave this year
        $user_id = auth()->user()->id;
        $errors = [];

        // Check for pending leave requests (status_1 = pending or status_2 = pending)
        $alreadyAppliedLeave = LeaveManagement::where('user_id', $user_id)
            ->where(function ($query) {
                $query->where('status_1', 'pending')  // Pending in first stage
                    ->orWhere(function ($query) {
                        $query->where('status_1', 'approved')  // Accepted in first stage
                            ->where('status_2', 'pending'); // Pending in second stage
                    });
            })
            ->exists(); // Use exists to check if any such leave exists

        if ($alreadyAppliedLeave) {
            $errors['already_applied_leave'] = 'You have an unprocessed leave request. Please wait until it is processed.';
        }


        // Check if "Birthday Leave" is selected in the request
        if (in_array(2, $request->full_day_leave ?? [])) { // 2 stands for "Birthday Leave"
            $currentYear = now()->year;
            $birthdayLeaveTaken = LeaveManagement::where('user_id', $user_id)
                ->whereJsonContains('leave_details', [['leave_type_id' => 2]])
                ->whereYear('created_at', $currentYear)
                ->exists();

            if ($birthdayLeaveTaken) {
                $errors['birthday_leave'] = 'You have already taken Birthday Leave this year.';
            }
        }

        // Check total "Marriage Leave" days taken in lifetime
        if (in_array(3, $request->full_day_leave ?? [])) { // 3 stands for "Marriage Leave"
            $marriageLeaveDaysTaken = LeaveManagement::where('user_id', $user_id)
                ->whereJsonContains('leave_details', [['leave_type_id' => 3]])
                ->get()
                ->sum(function ($leave) {
                    $details = json_decode($leave->leave_details, true);
                    return collect($details)->where('leave_type_id', 3)->sum(function ($marriageLeave) {
                        $start = new \DateTime($marriageLeave['start_date']);
                        $end = new \DateTime($marriageLeave['end_date']);
                        return $end->diff($start)->days + 1;
                    });
                });

            // Calculate new requested Marriage Leave days
            $newMarriageLeaveDaysRequested = 0;
            if (!empty($request->full_day_leave)) {
                foreach ($request->full_day_leave as $index => $leave_type) {
                    if ($leave_type == 3) {
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

        $annual_leave_balance = (float) $request->annual_leave_balance; // Ensure it's a float to handle fractional part
        $off_days = $request->off_days ?? [];
        $leave_details = [];

        // Process each full-day leave request
        if (!empty($request->full_day_leave)) {
            foreach ($request->full_day_leave as $index => $leave_type) {
                $from = $request->full_leave_from[$index];
                $to = $request->full_leave_to[$index];

                // Cast leave_type to integer
                $leave_type = (int) $leave_type;

                // Calculate the effective leave days excluding off-days
                $effective_days = $this->calculateLeaveDays($from, $to, $off_days);

                if ($leave_type == 1) { // Annual Leave
                    // Separate the integer and fractional parts of the balance
                    $whole_days_balance = floor($annual_leave_balance);
                    $fractional_balance = $annual_leave_balance - $whole_days_balance;

                    if ($effective_days > $whole_days_balance) {
                        // Calculate the maximum date up to which annual leave can be applied
                        $paid_end_date = $this->calculatePaidEndDate($from, $whole_days_balance, $off_days);

                        // Apply annual leave for the whole days only
                        if ($whole_days_balance > 0) {
                            $leave_details[] = [
                                'type' => 'full_day',
                                'leave_type_id' => 1,
                                'start_date' => $from,
                                'end_date' => $paid_end_date,
                                'status' => 'paid'
                            ];
                        }

                        // Remaining days after paid leave are converted to unpaid leave
                        $unpaid_start_date = (new \DateTime($paid_end_date))->modify('+1 day')->format('Y-m-d');
                        $leave_details[] = [
                            'type' => 'full_day',
                            'leave_type_id' => 4, // Unpaid Leave
                            'start_date' => $unpaid_start_date,
                            'end_date' => $to,
                            'status' => 'unpaid'
                        ];

                        // Reset the annual leave balance to the fractional part only (e.g., 0.5 day)
                        $annual_leave_balance = $fractional_balance;
                    } else {
                        // Entire period fits within Annual Leave balance (whole days + fractional part if applicable)
                        $leave_details[] = [
                            'type' => 'full_day',
                            'leave_type_id' => 1,
                            'start_date' => $from,
                            'end_date' => $to,
                            'status' => 'paid'
                        ];

                        // Deduct from Annual Leave balance
                        $annual_leave_balance -= $effective_days;
                    }
                } else {
                    // For other leave types, store as paid leave
                    $leave_details[] = [
                        'type' => 'full_day',
                        'leave_type_id' => $leave_type,
                        'start_date' => $from,
                        'end_date' => $to,
                        'status' => 'paid'
                    ];
                }
            }
        }

        // Process half-day leaves if present
        if (!empty($request->half_day_date)) {
            foreach ($request->half_day_date as $index => $half_day_date) {
                $start_time = $request->half_day_start_time[$index];
                $end_time = $request->half_day_end_time[$index];

                $leave_details[] = [
                    'type' => 'half_day',
                    'leave_type_id' => 1, // Assuming Annual Leave covers half-day leave
                    'date' => $half_day_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ];

                // Deduct 0.5 day from balance if there's any fractional part
                $annual_leave_balance -= 0.5;
            }
        }

        // Process off-days
        if (!empty($request->off_days)) {
            foreach ($request->off_days as $off_day) {
                $leave_details[] = [
                    'type' => 'off_day',
                    'date' => $off_day
                ];
            }
        }

        $assigners = AssignedLeaveApprovals::where('user_id', '=', $user_id)->first();
        // Check if the record is found, otherwise set empty or null values
        $first_assigners = ($assigners && !empty($assigners->first_assign_user_id))
            ? $assigners->first_assign_user_id
            : null; // or [] if you prefer an empty array

        $second_assigners = ($assigners && !empty($assigners->second_assign_user_id))
            ? $assigners->second_assign_user_id
            : null; // or [] if you prefer an empty array

        // Step 6: Save leave application to the database
        LeaveManagement::create([
            'user_id' => $user_id,
            'title' => $request->leave_title,
            'description' => $request->description,
            'leave_balance' => $request->annual_leave_balance,
            'leave_details' => json_encode($leave_details),
            'status_1' => 'pending',
            'status_2' => 'pending',
            'team_leader_ids' => $first_assigners,
            'manager_ids' => $second_assigners,
            'first_approval_id' => null,
            'first_approval_created_time' => null,
            'second_approval_id' => null,
            'second_approval_created_time' => null,
            'hr_approval_id' => null,
            'hr_approval_created_time' => null
        ]);

        return response()->json(['message' => 'Leave application submitted successfully.'], 200);
    }

    public function LeaveStatus(Request $request)
    {
        if ($request->ajax()) {
            // Get the authenticated user's ID
            $authId = auth()->user()->id;

            // Get all leave data for the authenticated user without filtering by status
            $leaves = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance', 'status_1', 'status_2', 'hr_approval_id', 'revoked', 'revoked_by', 'revoked_created_time'])
                ->where('user_id', $authId) // Filter by authenticated user
                ->get();

            // Return the leaves data as Datatables
            return Datatables::of($leaves)
                ->addIndexColumn()
                // Make Username & Employee ID searchable using the "whereHas" filter for joined fields
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

        // If it's not an AJAX request, return the view
        return view('leave_application.leave_status');
    }

    // Helper to calculate leave days excluding off-days
    private function calculateLeaveDays($start, $end, $off_days = [])
    {
        $start_date = new \DateTime($start);
        $end_date = new \DateTime($end);
        $days_count = 0;

        while ($start_date <= $end_date) {
            $current_date_str = $start_date->format('Y-m-d');

            if (!in_array($current_date_str, $off_days)) {
                $days_count++;
            }

            $start_date->modify('+1 day');
        }

        return $days_count;
    }

    // Helper to calculate the exact end date for Annual Leave based on balance (whole days only)
    private function calculatePaidEndDate($start, $annual_leave_balance, $off_days = [])
    {
        $current_date = new \DateTime($start);
        $days_used = 0;

        while ($days_used < $annual_leave_balance) {
            $current_date_str = $current_date->format('Y-m-d');

            if (!in_array($current_date_str, $off_days)) {
                $days_used++;
            }

            if ($days_used < $annual_leave_balance) {
                $current_date->modify('+1 day');
            }
        }

        return $current_date->format('Y-m-d');
    }

    public function display_leave(Request $request)
    {
        if ($request->ajax()) {
            // Get the authenticated user's ID
            $authId = auth()->user()->id;

            // Get the selected status from the request (if any)
            $status = $request->input('status', 'pending');  // default to 'pending' if no status is passed

            // Build the query based on the status and user role
            $leaves = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance'])
                ->where(function ($query) use ($authId, $status) {
                    if ($status === 'pending') {
                        // Query for pending leaves
                        $query->where(function ($subQuery) use ($authId) {
                            $subQuery->where('status_1', 'pending')
                                ->whereJsonContains('team_leader_ids', $authId);
                        })
                            ->orWhere(function ($subQuery) use ($authId) {
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'pending')
                                ->whereJsonContains('manager_ids', $authId);
                        });
                    } elseif ($status === 'approved') {
                        // Query for approved leaves where status_1 is approved and status_2 is either pending or approved
                        $query->where(function ($subQuery) use ($authId) {
                            // Only status_1 is approved, status_2 is pending (not yet processed)
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'pending')  // status_2 is pending, indicating it's not yet processed
                                ->where('revoked', '=', '0')
                                ->whereJsonContains('team_leader_ids', $authId); // Show for first step assigners (team leader)
                        })
                            ->orWhere(function ($subQuery) use ($authId) {
                            // Both status_1 and status_2 are approved, and the user should be in either team_leader_ids or manager_ids
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'approved')
                                ->where('revoked', '=', '0')
                                ->where(function ($innerSubQuery) use ($authId) {
                                // Allow the user to be in either team_leader_ids or manager_ids
                                $innerSubQuery->whereJsonContains('team_leader_ids', $authId)
                                    ->orWhereJsonContains('manager_ids', $authId);
                            });
                        });
                    } elseif ($status === 'rejected') {
                        // Query for rejected leaves where status_1 is rejected OR status_2 is rejected
                        $query->where(function ($subQuery) use ($authId) {
                            // If status_1 is rejected, and the user is in the team_leader_ids (step 1)
                            $subQuery->where('status_1', 'rejected')
                                ->whereJsonContains('team_leader_ids', $authId);
                        })
                            ->orWhere(function ($subQuery) use ($authId) {
                            // If status_2 is rejected, and the user is in the manager_ids (step 2)
                            $subQuery->where('status_2', 'rejected')
                                ->whereJsonContains('manager_ids', $authId);
                        })
                            ->orWhere(function ($subQuery) use ($authId) {
                            // If status_1 is approved but status_2 is rejected, and the user is in the team_leader_ids (step 1)
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'rejected')
                                ->whereJsonContains('team_leader_ids', $authId);
                        })
                            ->orWhere(function ($subQuery) use ($authId) {
                            // If status_1 is approved but status_2 is rejected, and the user is in the manager_ids (step 2)
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'rejected')
                                ->whereJsonContains('manager_ids', $authId);
                        });
                    } elseif ($status === 'revoked') {
                        // Query for approved leaves where status_1 is approved and status_2 is either pending or approved
                        $query->where(function ($subQuery) use ($authId) {
                            // Only status_1 is approved, status_2 is pending (not yet processed)
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'pending')  // status_2 is pending, indicating it's not yet processed
                                ->where('revoked', '=', '1')
                                ->whereJsonContains('team_leader_ids', $authId); // Show for first step assigners (team leader)
                        })
                            ->orWhere(function ($subQuery) use ($authId) {
                            // Both status_1 and status_2 are approved, and the user should be in either team_leader_ids or manager_ids
                            $subQuery->where('status_1', 'approved')
                                ->where('status_2', 'approved')
                                ->where('revoked', '=', '1')
                                ->where(function ($innerSubQuery) use ($authId) {
                                // Allow the user to be in either team_leader_ids or manager_ids
                                $innerSubQuery->whereJsonContains('team_leader_ids', $authId)
                                    ->orWhereJsonContains('manager_ids', $authId);
                            });
                        });
                    }

                })
                ->get();

            // Return the leaves data as Datatables
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
        $second_approval_username = getUser($leave->second_approval_id);
        $hr_approval_username = getUser($leave->hr_approval_id);
        $revoked_approval_username = getUser($leave->revoked_by);
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
            // Get HR ID
            'hr_approval_id' => $hr_approval_username ?? "Null",
            'hr_approval_created_time' => $leave->hr_approval_created_time ?? "YYYY-MM-DD HH:MM:SS",
            //Get Revoked ID
            'revoked' => $leave->revoked,
            'revoked_by' => $revoked_approval_username ?? "Null",
            'revoked_created_time' => $leave->revoked_created_time ?? "YYYY-MM-DD HH:MM:SS",


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
                        } elseif ($leave['type'] === 'full_day' && in_array($leaveTypeId, [2, 3, 4, 5, 6, 7, 8])) {
                            // For other full-day leaves that are not Annual Leave (Birthday, Marriage, Unpaid)
                            $startDate = new \DateTime($leave['start_date']);
                            $endDate = new \DateTime($leave['end_date']);
                            $days = $startDate->diff($endDate)->days + 1;

                            for ($i = 0; $i < $days; $i++) {
                                $currentDate = $startDate->format('Y-m-d');

                                // Check for off days for unpaid leave as well
                                $isOffDay = collect($leaveDetails)->contains(function ($detail) use ($currentDate) {
                                    return $detail['type'] === 'off_day' && $detail['date'] === $currentDate;
                                });

                                if (!$isOffDay) {
                                    // Add this day to the approved_leaves table without deduction for off days
                                    DB::table('approved_leaves')->insert([
                                        'user_id' => $leaveUpdate->user_id,
                                        'leave_type' => $leaveTypeId, // Store as integer for type (2 = BL, 3 = ML, 4 = UL)
                                        'date' => $currentDate,
                                    ]);
                                }

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

    public function UnassignedLeaveIndex(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            // Build the query based on the status and user role
            $leaves = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance'])
                ->where(function ($query) {
                    // Check for 'pending' status
                    $query->where('status_1', 'pending')
                        // Check if 'team_leader_ids' is NULL or an empty array
                        ->where(function ($subQuery) {
                        $subQuery->whereNull('team_leader_ids')
                            ->orWhereJsonLength('team_leader_ids', 0);
                    });
                })
                ->get();

            // Return the leaves data as Datatables
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

        return view('leave_application.unassigned_leave_applications', compact('users'));
    }

    public function AddUnassignedLeave(Request $request)
    {
        // Validate the input fields
        $validator = Validator::make($request->all(), [
            'team_leader_ids' => 'required|array|min:1',
            'team_leader_ids.*' => 'integer|exists:users,id',  // Validate each ID is an integer and exists in users table
            'manager_ids' => 'required|array|min:1',
            'manager_ids.*' => 'integer|exists:users,id',      // Validate each ID is an integer and exists in users table
        ]);

        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $leaveManagement = LeaveManagement::find($request->leaveApprovalId);

        // If the leave record does not exist, return an error response
        if (!$leaveManagement) {
            return response()->json(['success' => false, 'message' => 'Leave record not found.'], 404);
        }

        $leaveApplierID = $leaveManagement->user_id;

        // Convert team_leader_ids and manager_ids from usernames to user IDs
        $teamLeaderUsernames = $request->team_leader_ids;
        $managerUsernames = $request->manager_ids;

        // Convert the usernames (or user IDs) to integers
        $teamLeaderIds = array_map('intval', $teamLeaderUsernames);  // Convert each ID to an integer
        $managerIds = array_map('intval', $managerUsernames);        // Convert each ID to an integer

        // Encode the arrays as JSON (now with integer values)
        $leaveManagement->team_leader_ids = json_encode($teamLeaderIds); // Store as JSON array of integers
        $leaveManagement->manager_ids = json_encode($managerIds);

        // Save the changes
        $leaveManagement->save();

        $leaveApprovals = AssignedLeaveApprovals::where('user_id', '=', $leaveApplierID)->first();
        $leaveApprovals->first_assign_user_id = json_encode($teamLeaderIds);
        $leaveApprovals->second_assign_user_id = json_encode($managerIds);
        $leaveApprovals->save(); // Save the changes

        // Return success response
        return response()->json(['success' => true, 'message' => 'Leave assigned successfully.']);
    }

    public function multiSelect(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);  // Get the current page, default to 1

        // If there is no search term, return the first 10 records
        if (!$search) {
            $data = User::where('status', '1')
                ->paginate(10, ['*'], 'page', $page);  // Paginate, 10 items per page
        } else {
            $data = User::when($search, function ($query) use ($search) {
                return $query->where('username', 'LIKE', '%' . $search . '%');
            })
                ->where('status', '1')
                ->paginate(10, ['*'], 'page', $page);
        }

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
        ]);
    }
    // **************************************************************   HR Part Starts Here  *************************************************************************************
    public function leave_view_hr()
    {
        return view('leave_application.hr_leave_work');
    }

    public function display_leave_hr(Request $request)
    {
        if ($request->ajax()) {
            // Get the authenticated user's ID
            $authId = auth()->user()->id;

            // Get the selected status from the request (if any)
            $status = $request->input('status', 'pending');  // default to 'pending' if no status is passed

            // Build the query based on the status and user role
            $leaves = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance', 'status_1', 'status_2', 'hr_approval_id'])
                ->when($status === 'pending', function ($query) {
                    // Pending requests: status_1 is pending OR status_2 is pending, but not rejected
                    $query->where(function ($subQuery) {
                        $subQuery->where('status_1', 'pending')
                            ->orWhere('status_2', 'pending');
                    })->whereNotIn('status_1', ['rejected'])
                        ->whereNotIn('status_2', ['rejected']);
                })
                ->when($status === 'approved', function ($query) {
                    // Approved requests: Both status_1 and status_2 are approved
                    $query->where('status_1', 'approved')
                        ->where('status_2', 'approved')
                        ->where('revoked', '=', '0')
                        ->whereNull('hr_approval_id');
                })
                ->when($status === 'rejected', function ($query) {
                    // Rejected requests: Either status_1 or status_2 is rejected
                    $query->where(function ($subQuery) {
                        $subQuery->where('status_1', 'rejected')
                            ->orWhere('status_2', 'rejected');
                    });
                })
                ->when($status === 'completed', function ($query) {
                    // Completed requests: Both status_1 and status_2 are approved and hr_approval_id exists
                    $query->where('status_1', 'approved')
                        ->where('status_2', 'approved')
                        ->where('revoked', '=', '0')
                        ->whereNotNull('hr_approval_id');
                })
                ->when($status === 'revoked', function ($query) {
                    // Completed requests: Both status_1 and status_2 are approved and hr_approval_id exists
                    $query->where('status_1', 'approved')
                        ->where('status_2', 'approved')
                        ->where('revoked', '=', '1');
                })

                ->get();

            // Return the leaves data as Datatables
            return Datatables::of($leaves)
                ->addIndexColumn()
                // Make Username & Employee ID searchable using the "whereHas" filter for joined fields

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

    public function leave_hr_workdone(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'leave_id' => 'required|integer|exists:leave_management,id', // Ensure the leave_id exists in the database
            'leave_action' => 'required|string|in:hr_done_request', // Only allow 'approve' or 'reject'
        ]);

        // Retrieve the validated data from the request
        $leaveId = $request->leave_id;
        $leaveAction = $request->leave_action;

        // Get the authenticated HR user ID
        $activeHrId = auth()->user()->id;

        // Fetch the leave request information from the database
        $leaveRequest = LeaveManagement::findOrFail($leaveId); // Using findOrFail to throw an exception if the leave doesn't exist

        // Update the leave request with the HR approval details
        $leaveRequest->hr_approval_id = $activeHrId;
        $leaveRequest->hr_approval_created_time = now(); // Using "created_at" is more conventional in Laravel for timestamps
        $leaveRequest->save();

        // Prepare the response data
        $response = [
            'leave_id' => $leaveId,
            'leave_action' => $leaveAction,
            'approved_by' => $activeHrId,
            'leave_request' => $leaveRequest // Return the updated leave request data
        ];

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Leave request successfully updated',
            'data' => $response
        ]);
    }

    // **************************************************************   CUSTOM LEAVE SEARCH  *************************************************************************************
    public function search(Request $request)
    {
        $user = auth()->user();  // Get the currently authenticated user

        // If the user does not have role 1, 2, or 3, return an error response
        if (!in_array($user->role, [1, 2, 3])) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.']);
        }

        $request->validate([
            'user_id' => 'required|integer',
            'employee_name' => 'required|string'
        ]);

        // Find the user by employee_id or username
        $user = User::where('employee_id', $request->employee_name)->orWhere('username', $request->employee_name)->first();

        if ($user) {
            // Get the leaves for the found user
            $leaves = LeaveManagement::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(8)->get();

            // Map over the leaves to extract leave details
            $responseData = $leaves->map(function ($leave) {
                // Decode the JSON stored in the leave_details column for each leave
                $leaveDetails = json_decode($leave->leave_details, true);

                $totalLeaveDays = 0;  // To accumulate total leave days
                $leaveFrom = [];
                $leaveTo = [];

                // Loop through each leave type and extract the start_date and end_date
                foreach ($leaveDetails as $detail) {
                    $startDate = isset($detail['start_date']) ? $detail['start_date'] : null;
                    $endDate = isset($detail['end_date']) ? $detail['end_date'] : null;

                    if ($startDate && $endDate) {
                        // Optionally, you can calculate the leave days for each range
                        $totalLeaveDays += $this->calculateLeaveDays($startDate, $endDate);
                        $leaveFrom[] = $startDate;
                        $leaveTo[] = $endDate;
                    }
                }


                // $currentStatus = $leave->status_2 ?: $leave->status_1;
                if ($leave->status_1 == 'rejected') {
                    $currentStatus = 'Rejected';
                } elseif ($leave->status_1 == 'approved' && $leave->status_2 == 'pending') {
                    $currentStatus = 'Pending';
                } elseif ($leave->status_1 == 'approved' && $leave->status_2 == 'rejected') {
                    $currentStatus = 'Rejected';
                } elseif ($leave->status_1 == 'approved' && $leave->status_2 == 'approved') {
                    $currentStatus = 'Approved';
                } elseif ($leave->status_1 == 'pending' && $leave->status_2 == 'pending') {
                    $currentStatus = 'Pending';
                } elseif ($leave->revoked === '1') {
                    $currentStatus = "Revoked";
                } else {
                    $currentStatus = 'Unknown'; // For any unexpected status combination
                }
                // Return the structured response
                return [
                    'name' => $leave->user->username, // Assuming leave record has a relation to User
                    'employee_id' => $leave->user->employee_id,
                    'leave_balance' => $leave->leave_balance,
                    'leave_days' => $totalLeaveDays,  // Total calculated leave days from all leave types
                    'leave_title' => $leave->title,
                    'leave_description' => $leave->description,
                    'leave_from' => implode(', ', $leaveFrom), // Combine all start dates into a string
                    'leave_to' => implode(', ', $leaveTo),     // Combine all end dates into a string
                    'leave_status_1' => $leave->status_1,
                    'leave_status_2' => $leave->status_2,
                    'leave_status' => $currentStatus,
                    'leave_id' => $leave->id,
                    'user_id' => $leave->user_id,
                    'revoked' => $leave->revoked,
                ];
            });

            // Return the response as JSON
            return response()->json(['success' => true, 'data' => $responseData]);
        }

        // If user not found
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    public function customLeaveDetail()
    {
        return view('leave_application.custom_search_detail'); // Pass as 'leaves'
    }

    public function allLeaves(Request $request)
    {
        $userID = $request->userID;
        $leaveID = $request->leaveID;

        if ($userID) {
            $leave = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance'])
                ->where('user_id', $userID)
                ->get();
        }

        if ($leaveID) {
            $leave = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance'])
                ->where('id', $leaveID)
                ->get();
        }

        if ($leave) {
            // Return the leaves data as Datatables
            return Datatables::of($leave)
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
        return view('leave_application.custom_search_detail');
    }

    // ***************************** CUSTOM LEAVE SEARCH  ******************************************************************
}
