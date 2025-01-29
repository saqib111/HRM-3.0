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
            'revoked_al_balance' => 'required|numeric|min:0',           // Ensure revoked balance is numeric and non-negative
        ]);

        // Retrieve the validated data from the request
        $leaveId = $request->leave_id;
        $leaveAction = $request->leave_action;
        $revoked_al_balance = $request->revoked_al_balance;

        // Get the authenticated user's ID (the person revoking the leave)
        $activeRevokeId = auth()->user()->id;

        // Fetch the leave request information from the database
        $leaveRequest = LeaveManagement::findOrFail($leaveId); // Will throw an exception if the leave doesn't exist

        // Assuming AnnualLeaves table has a `user_id` field to match with LeaveManagement user_id
        $annualLeave = AnnualLeaves::where('user_id', $leaveRequest->user_id)->first();

        if ($annualLeave) {
            // Add the total days to add back to the AnnualLeave balance
            $annualLeave->leave_balance += $revoked_al_balance;
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
