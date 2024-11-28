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
            // Fetch the leave approvals using server-side pagination
            $leaveApprovals = AssignedLeaveApprovals::select(
                'assigned_leave_approvals.*',
                'requesting_user.username as user_name'
            )
                ->leftJoin('users as requesting_user', 'assigned_leave_approvals.user_id', '=', 'requesting_user.id');

            // Implement pagination using DataTables request parameters
            $totalRecords = $leaveApprovals->count(); // Total records without filtering
            $leaveApprovals = $leaveApprovals->skip($request->start)
                ->take($request->length);

            // Fetching the leave approval data
            $leaveApprovals = $leaveApprovals->get();

            // Prepare names for first and second assigned users
            foreach ($leaveApprovals as $approval) {
                $firstAssignedUserIds = json_decode($approval->first_assign_user_id, true) ?? [];
                $secondAssignedUserIds = json_decode($approval->second_assign_user_id, true) ?? [];

                $firstAssignedUserNames = !empty($firstAssignedUserIds)
                    ? User::whereIn('id', $firstAssignedUserIds)->pluck('username')->toArray()
                    : null;

                $secondAssignedUserNames = !empty($secondAssignedUserIds)
                    ? User::whereIn('id', $secondAssignedUserIds)->pluck('username')->toArray()
                    : null;

                $approval->first_assigned_user_name = $firstAssignedUserNames; // Store as array or null
                $approval->second_assigned_user_name = $secondAssignedUserNames; // Store as array or null
            }

            // DataTables needs a response in the following structure
            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords, // Total records count (without filtering)
                'recordsFiltered' => $totalRecords, // You can implement filtering if needed
                'data' => $leaveApprovals
            ]);
        }

        // Fetch all users for the view
        $users = User::select('id', 'username')->get();

        return view('leaveReassigners.leave-approvals', compact('users'));
    }

    public function searchAssigner(Request $request)
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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }



    public function edit($id)
    {
        // Fetch the leave approval data by ID
        $leaveApproval = AssignedLeaveApprovals::find($id);

        // Check if the leave approval record exists
        if (!$leaveApproval) {
            return response()->json(['error' => 'Leave approval not found'], 404);
        }

        // Fetch the usernames of the assigned users
        $firstAssignedUsers = User::whereIn('id', json_decode($leaveApproval->first_assign_user_id, true) ?: [])
            ->pluck('username', 'id') // Retrieve the username based on user IDs
            ->toArray();

        $secondAssignedUsers = User::whereIn('id', json_decode($leaveApproval->second_assign_user_id, true) ?: [])
            ->pluck('username', 'id') // Retrieve the username based on user IDs
            ->toArray();

        // Return the data as JSON, ensuring to decode the JSON encoded user IDs for front-end
        return response()->json([
            'leaveApprovalId' => $leaveApproval->id,
            'first_assigned_user' => $firstAssignedUsers,
            'second_assigned_user' => $secondAssignedUsers,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'first_assigned_user' => 'nullable|array',  // Allow null or array
            'second_assigned_user' => 'nullable|array', // Allow null or array
            'leaveApprovalId' => 'required|integer', // Ensure leaveApprovalId is provided
        ]);

        // Find the leave approval by ID
        $leaveApproval = AssignedLeaveApprovals::find($validated['leaveApprovalId']);

        if (!$leaveApproval) {
            return response()->json(['error' => 'Leave approval not found'], 404);
        }

        // Handle first assigned user
        if (empty($validated['first_assigned_user'])) {
            $leaveApproval->first_assign_user_id = null;
        } else {
            // Ensure all elements are integers before encoding them back into JSON
            $leaveApproval->first_assign_user_id = json_encode(array_map('intval', $validated['first_assigned_user']));
        }

        // Handle second assigned user
        if (empty($validated['second_assigned_user'])) {
            $leaveApproval->second_assign_user_id = null;
        } else {
            // Ensure all elements are integers before encoding them back into JSON
            $leaveApproval->second_assign_user_id = json_encode(array_map('intval', $validated['second_assigned_user']));
        }

        // Save the changes
        $leaveApproval->save();

        // Return a success response
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