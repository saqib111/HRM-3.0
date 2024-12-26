<?php

namespace App\Http\Controllers;
use App\Models\AnnualLeaves;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Http\Request;

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
            $query = AnnualLeaves::join('users', 'annual_leaves.user_id', '=', 'users.id')
                ->select([
                    'annual_leaves.id',
                    'users.username as username',
                    'annual_leaves.leave_type',
                    'annual_leaves.leave_balance',
                    'annual_leaves.last_year_balance'
                ]);

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
        $AnnualLeaves->update($validatedData);

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