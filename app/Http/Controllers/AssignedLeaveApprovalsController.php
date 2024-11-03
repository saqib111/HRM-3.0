<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignedLeaveApprovals;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class AssignedLeaveApprovalsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fetch leave approvals for DataTables
            $leaveApprovals = AssignedLeaveApprovals::select(
                'assigned_leave_approvals.*',
                'requesting_user.username as user_name'
            )
                ->leftJoin('users as requesting_user', 'assigned_leave_approvals.user_id', '=', 'requesting_user.id')
                ->get();

            // Prepare names for first and second assigned users
            foreach ($leaveApprovals as $approval) {
                $firstAssignedUserIds = json_decode($approval->first_assign_user_id, true);
                $secondAssignedUserIds = json_decode($approval->second_assign_user_id, true);

                $firstAssignedUserNames = User::whereIn('id', $firstAssignedUserIds)->pluck('username')->toArray();
                $secondAssignedUserNames = User::whereIn('id', $secondAssignedUserIds)->pluck('username')->toArray();

                $approval->first_assigned_user_name = $firstAssignedUserNames; // Store as array
                $approval->second_assigned_user_name = $secondAssignedUserNames; // Store as array
            }

            return DataTables::of($leaveApprovals)->make(true);
        }

        // Fetch all users
        $users = User::select('id', 'username')->get();

        // Pass both leave approvals and users to the view
        return view('leaveReassigners.leave-approvals', compact('users'));
    }






    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'first_assigned_user' => 'required|array',
            'second_assigned_user' => 'required|array',
            'leaveApprovalId' => 'required|integer', // Assuming you're also passing the leave approval ID
        ]);

        // You can save the assignments to the database
        $leaveApproval = AssignedLeaveApprovals::find($validated['leaveApprovalId']);
        if ($leaveApproval) {
            // Assuming you have methods to set these values
            $leaveApproval->first_assign_user_id = json_encode($validated['first_assigned_user']);
            $leaveApproval->second_assign_user_id = json_encode($validated['second_assigned_user']);
            $leaveApproval->save();
        }

        return response()->json(['success' => true]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Find the leave approval record
        $leaveApproval = AssignedLeaveApprovals::find($id);

        if (!$leaveApproval) {
            return response()->json(['success' => false, 'message' => 'Leave approval not found.'], 404);
        }

        // Return the assigned user IDs as an array
        return response()->json([
            'success' => true,
            'data' => [
                'first_assigned_user_id' => json_decode($leaveApproval->first_assign_user_id, true),
                'second_assigned_user_id' => json_decode($leaveApproval->second_assign_user_id, true),
            ],
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'first_assigned_user' => 'required|array',
            'second_assigned_user' => 'required|array',
        ]);

        // Find the leave approval record
        $leaveApproval = AssignedLeaveApprovals::find($id);
        if ($leaveApproval) {
            $leaveApproval->first_assign_user_id = json_encode($validated['first_assigned_user']);
            $leaveApproval->second_assign_user_id = json_encode($validated['second_assigned_user']);
            $leaveApproval->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}