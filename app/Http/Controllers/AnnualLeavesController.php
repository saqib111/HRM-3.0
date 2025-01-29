<?php

namespace App\Http\Controllers;
use App\Models\AnnualLeaves;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnualLeavesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();

            // Fetch user permissions
            $permissions = getUserPermissions($user);
            $canUpdateLeaveBalance = $user->role == 1 || in_array('update_al_balance', $permissions);

            // Query with join
            if ($user->role == 1) {
                $query = AnnualLeaves::join('users', 'annual_leaves.user_id', '=', 'users.id')
                    ->select([
                        'annual_leaves.id',
                        'users.username as username',
                        'annual_leaves.leave_type',
                        'annual_leaves.leave_balance',
                        'annual_leaves.last_year_balance'
                    ]);
            } elseif ($user->role == 2) {
                $query = AnnualLeaves::join('users', 'annual_leaves.user_id', '=', 'users.id')
                    ->select([
                        'annual_leaves.id',
                        'users.username as username',
                        'annual_leaves.leave_type',
                        'annual_leaves.leave_balance',
                        'annual_leaves.last_year_balance'
                    ]);
            } elseif ($user->role == 4) {
                $leader_id = $user->id;

                $query = AnnualLeaves::join('users', 'annual_leaves.user_id', '=', 'users.id')
                    ->join('leader_employees', 'leader_employees.employee_id', '=', 'users.id') // Use the correct column here
                    ->where('leader_employees.leader_id', $leader_id) // Filter records for the specific leader_id
                    ->select([
                        'annual_leaves.id',
                        'users.username as username',
                        'annual_leaves.leave_type',
                        'annual_leaves.leave_balance',
                        'annual_leaves.last_year_balance'
                    ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()

                // Fix search for "username"
                ->filterColumn('username', function ($query, $keyword) {
                    $query->where('users.username', 'LIKE', "%{$keyword}%");
                })

                ->addColumn('can_update', function () use ($canUpdateLeaveBalance) {
                    return $canUpdateLeaveBalance; // Include permission flag in response
                })
                ->rawColumns(['can_update']) // Allow HTML in this column if needed
                ->make(true);
        }

        return view('annualLeaveBalance.annual-leaves');
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $AnnualLeaves = AnnualLeaves::find($id); // Adjust according to your model and database
        return response()->json($AnnualLeaves);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'leave_balance' => 'required',
        ]);

        $AnnualLeaves = AnnualLeaves::findOrFail($id);
        // Capture the old leave balance before update
        $oldLeaveBalance = $AnnualLeaves->leave_balance;

        $AnnualLeaves->update($validatedData);


        Log::channel('update_ALBalance')->info(
            "Updated By: ID: " . auth()->user()->id .
            " | Username: " . auth()->user()->username .
            " | Employee ID: " . auth()->user()->employee_id . "\n" .  // Log all details in one line

            "Updated Employee AL Balance: " .
            "User ID: " . $AnnualLeaves->user_id . " | " .  // User ID
            "Username: " . $AnnualLeaves->user->username . " | " .  // Username
            "Employee ID: " . $AnnualLeaves->user->employee_id . "\n" .  // Employee ID

            "Leave Balance Update\n" .
            "Old Leave Balance: " . $oldLeaveBalance . "\n" .  // Old leave balance
            "New Leave Balance: " . $validatedData['leave_balance'] . "\n" .  // New leave balance
            "Updated At: " . now()->toDateTimeString() . "\n\n"  // Timestamp of the update
        );



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