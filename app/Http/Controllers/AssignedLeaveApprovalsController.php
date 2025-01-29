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

            // If there's a search term, apply it to the user_name column
            if ($request->has('search') && $request->search['value']) {
                $searchTerm = $request->search['value'];
                $leaveApprovals = $leaveApprovals->where('requesting_user.username', 'like', '%' . $searchTerm . '%');
            }
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

    // CUSTOM-MULTI CONTROLLER CODE.
    public function getUsers(Request $request)
    {
        // Get the search term and page number
        $searchTerm = $request->input('searchTerm', '');
        $page = $request->input('page', 1);

        // Query users with pagination and search
        $usersQuery = User::query();

        if ($searchTerm) {
            $usersQuery->where('username', 'like', '%' . $searchTerm . '%');
        }

        $users = $usersQuery->paginate(10, ['id', 'username'], 'page', $page);

        return response()->json([
            'data' => $users->items(),
            'last_page' => $users->lastPage(),
        ]);
    }


    // STORE THE (ASSIGNERS) DATA IN TABLE
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'first_assigned_user' => 'nullable|array',  // Allow null or array
            'second_assigned_user' => 'nullable|array', // Allow null or array
            'leaveApprovalId' => 'required|integer', // Ensure leaveApprovalId is provided
        ]);

        // Find or create the leave approval record
        $leaveApproval = AssignedLeaveApprovals::find($validated['leaveApprovalId']);

        if (!$leaveApproval) {
            // If no leave approval exists, create a new one
            $leaveApproval = new AssignedLeaveApprovals();
            $leaveApproval->leaveApprovalId = $validated['leaveApprovalId'];
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



    // YourController.php
    public function edit($id)
    {
        try {
            $leaveApproval = AssignedLeaveApprovals::find($id);

            if (!$leaveApproval) {
                return response()->json(['error' => 'Leave approval not found'], 404);
            }

            // Log the values before decoding
            \Log::info('First Assigned User ID: ' . $leaveApproval->first_assign_user_id);
            \Log::info('Second Assigned User ID: ' . $leaveApproval->second_assign_user_id);

            $firstAssignedUsers = User::whereIn('id', json_decode($leaveApproval->first_assign_user_id, true) ?: [])
                ->pluck('username', 'id')
                ->toArray();

            $secondAssignedUsers = User::whereIn('id', json_decode($leaveApproval->second_assign_user_id, true) ?: [])
                ->pluck('username', 'id')
                ->toArray();

            return response()->json([
                'leaveApprovalId' => $leaveApproval->id,
                'first_assigned_user' => $firstAssignedUsers,
                'second_assigned_user' => $secondAssignedUsers,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in edit controller: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
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