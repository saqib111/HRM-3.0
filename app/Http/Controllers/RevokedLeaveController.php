<?php

namespace App\Http\Controllers;

use App\Models\ApprovedLeave;
use Illuminate\Http\Request;
use App\Models\LeaveManagement;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Models\AnnualLeaves;
use Carbon\Carbon;

class RevokedLeaveController extends Controller
{
    public function getRevokedLeave(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status', 'approved'); // Default to 'approved' if no status is passed

            $leaves = LeaveManagement::with('user')
                ->select(['id', 'user_id', 'title', 'description', 'leave_details', 'leave_balance', 'status_1', 'status_2', 'hr_approval_id', 'revoked'])
                ->when($status === 'approved', function ($query) {
                    // Approved section: status_1 and status_2 must be 'approved' AND revoked is 0
                    $query->where('status_1', 'approved')
                        ->where('status_2', 'approved')
                        ->where('revoked', '0'); // Only show leaves that are NOT revoked
                })
                ->when($status === 'revoked', function ($query) {
                    // Revoked section: status_1 and status_2 must be 'approved' AND revoked is 1
                    $query->where('status_1', 'approved')
                        ->where('status_2', 'approved')
                        ->where('revoked', '1'); // Only show leaves that are revoked
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

        return view('leave_application.revoked_leave');
    }


    public function RevokedLeaveBtn(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'leave_id' => 'required|integer|exists:leave_management,id', // Ensure the leave_id exists in the database
            'leave_action' => 'required|string|in:revoke_request', // Only allow the action that should trigger the update
        ]);

        // Retrieve the validated data from the request
        $leaveId = $request->leave_id;
        $leaveAction = $request->leave_action;

        // Get the authenticated user's ID (the person revoking the leave)
        $activeRevokeId = auth()->user()->id;

        // Fetch the leave request information from the database
        $leaveRequest = LeaveManagement::findOrFail($leaveId); // Will throw an exception if the leave doesn't exist

        // Check if leave_details field (JSON) exists and contains leave data
        $leaveDetails = json_decode($leaveRequest->leave_details, true);

        // Initialize a variable to store the total number of days for leave_type_id == 1 (Full Day)
        $totalDaysToAddBack = 0;

        // Check if leave details exist and are in the correct format
        if ($leaveDetails && is_array($leaveDetails)) {
            // Iterate through the leave details and delete the corresponding leaves from approved_leaves
            foreach ($leaveDetails as $detail) {
                if (isset($detail['start_date']) && isset($detail['end_date'])) {
                    // If the leave is of type 'full_day' and the leave_type_id is 1, we'll add it back to the balance
                    if ($detail['type'] === 'full_day' && $detail['leave_type_id'] == 1) {
                        // Calculate the number of days between start_date and end_date
                        $startDate = Carbon::parse($detail['start_date']);
                        $endDate = Carbon::parse($detail['end_date']);
                        $daysCount = $startDate->diffInDays($endDate) + 1; // +1 to include the start date

                        // Accumulate the total days to add back to the AnnualLeave balance
                        $totalDaysToAddBack += $daysCount;

                        // Optionally, mark the leave as revoked in details
                        $detail['status'] = 'revoked'; // Mark the leave as revoked in details
                    }

                    // Find and delete the approved leaves for the same user and leave dates
                    ApprovedLeave::where('user_id', $leaveRequest->user_id)
                        ->whereIn('leave_type', [1, 2, 3, 4, 5, 6, 7, 8]) // Ensure matching leave types
                        ->whereBetween('date', [$detail['start_date'], $detail['end_date']])
                        ->delete(); // Deletes all matching records
                }
            }

            // Update leave_details with revoked status for the revoked leaves
            $leaveRequest->leave_details = json_encode($leaveDetails);
        }

        // Assuming AnnualLeaves table has a `user_id` field to match with LeaveManagement user_id
        $annualLeave = AnnualLeaves::where('user_id', $leaveRequest->user_id)->first();

        if ($annualLeave) {
            // Add the total days to add back to the AnnualLeave balance
            $annualLeave->leave_balance += $totalDaysToAddBack;
            $annualLeave->save(); // Save the updated balance to the AnnualLeaves table
        } else {
            // If no AnnualLeaves entry is found, handle as needed
            return response()->json([
                'success' => false,
                'message' => 'Annual leave entry not found for the user.',
            ]);
        }

        // Update the leave request with the revoked details
        $leaveRequest->revoked_by = $activeRevokeId;  // Store the ID of the person revoking the leave
        $leaveRequest->revoked = '1';  // Set 'revoked' status to 1 (indicating the leave is revoked)
        $leaveRequest->revoked_created_time = now();
        $leaveRequest->save();  // Save the changes to the database

        // Prepare the response data
        $response = [
            'leave_id' => $leaveId,
            'leave_action' => $leaveAction,
            'revoked_by' => $activeRevokeId,
            'revoked' => '1',  // Indicating that this leave is now revoked
        ];

        // Return a success response with the updated data
        return response()->json([
            'success' => true,
            'message' => 'Leave request successfully updated and revoked, corresponding approved leaves removed, and balance updated.',
            'data' => $response,
        ]);
    }

}
