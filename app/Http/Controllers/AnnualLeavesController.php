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
            $data = AnnualLeaves::join('users', 'annual_leaves.user_id', '=', 'users.id')
                ->select(['annual_leaves.id', 'users.username as username', 'annual_leaves.leave_type', 'annual_leaves.leave_balance', 'annual_leaves.last_year_balance']);

            return DataTables::of($data)->make(true);
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