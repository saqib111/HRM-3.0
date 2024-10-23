<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveManagement;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LeaveController extends Controller
{
    public function leave_form()
    {
        return view('leave_application.leave_form');
    }

    // Leave Application Backend Starts From Here.
    public function store_leave(Request $request)
    {
        // Step 1: Validate the request using Validator for AJAX
        $validator = Validator::make($request->all(), [
            'leave_title' => 'required|string|max:255',
            'annual_leave_balance' => 'required|integer',
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

        // Step 3: Get form inputs
        $leave_title = $request->leave_title;
        $description = $request->description;
        $user_id = auth()->user()->id; // Assuming the user is authenticated
        $annual_leave_balance = $request->annual_leave_balance;

        // Step 4: Prepare leave details array
        $leave_details = [];

        // Full-day leave processing
        if (!empty($request->full_day_leave)) {
            foreach ($request->full_day_leave as $index => $leave_type) {
                $from = $request->full_leave_from[$index];
                $to = $request->full_leave_to[$index];

                // Handle unpaid leave if the user exceeds their annual leave balance
                $total_days = $this->calculateLeaveDays($from, $to, $request->off_days ?? []);
                if ($leave_type == 1 && $total_days > $annual_leave_balance) { // 1 is for "Annual Leave"
                    // Store the part covered by annual leave
                    $leave_details[] = [
                        'type' => 'full_day',
                        'leave_type_id' => 1, // Annual Leave
                        'start_date' => $from,
                        'end_date' => $this->calculateRemainingUnpaidStartDate($from, $annual_leave_balance - 1),
                        'status' => 'paid'
                    ];

                    // Store the unpaid part
                    $leave_details[] = [
                        'type' => 'full_day',
                        'leave_type_id' => 4, // Unpaid Leave
                        'start_date' => $this->calculateRemainingUnpaidStartDate($from, $annual_leave_balance),
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

        // Step 5: Save leave application to the database
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

        // Step 6: Return success response for AJAX
        return response()->json(['message' => 'Leave application submitted successfully.'], 200);
    }

    // Helper method to calculate leave days excluding off-days
    private function calculateLeaveDays($start, $end, $off_days = [])
    {
        $start_date = new \DateTime($start);
        $end_date = new \DateTime($end);
        $interval = $start_date->diff($end_date);
        $days = $interval->days + 1;

        foreach ($off_days as $off_day) {
            $off_day_date = new \DateTime($off_day);
            if ($off_day_date >= $start_date && $off_day_date <= $end_date) {
                $days--; // Subtract off-day from total leave days
            }
        }

        return $days;
    }

    // Helper method to calculate the start date of unpaid leave
    private function calculateRemainingUnpaidStartDate($start, $annual_leave_balance)
    {
        $start_date = new \DateTime($start);
        return $start_date->modify("+$annual_leave_balance days")->format('Y-m-d');
    }
    // Leave Application Backend Ends From Here.


    public function display_leave(Request $request)
    {
        if ($request->ajax()) {
            $leaves = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details']);

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

        return response()->json([
            'employee_id' => $leave->user->employee_id,
            'username' => $leave->user->username,
            'title' => $leave->title,
            'description' => $leave->description,
            'leave_details' => json_decode($leave->leave_details), // Ensure leave details are properly decoded
        ]);
    }

}
