<?php

namespace App\Http\Controllers;

use App\Models\LeaderEmployee;
use Illuminate\Http\Request;
use App\Models\{

    User,


};
use Illuminate\Support\Facades\{
    DB,
    Session,
    Http
};
class LeaderEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $leader_id = explode(',', $request->leader_id);
        ;
        $employee_id = explode(',', $request->employee_id);


        if (count($leader_id) > 0) {


            foreach ($leader_id as $ld) {

                $leaderCheck = DB::table('leader_employees')
                    ->select('id')
                    ->where('Leader_id', $ld)
                    ->first();
                if ($leaderCheck != null) {
                    return response()->json([
                        'message' => 'Team Leader already Exist!'
                    ], 300);
                } else {
                    if (count($employee_id) > 0) {
                        foreach ($employee_id as $ei) {
                            $insert = new LeaderEmployee();
                            $insert->Leader_id = $ld;
                            $insert->employee_id = $ei;
                            $insert->save();



                        }
                    }
                }

            }
            return response()->json([
                'message' => 'Team created successfully!'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaderEmployee $leaderEmployee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaderEmployee $leaderEmployee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $leader_id = explode(',', $request->leader_id);
        ;
        $employee_id = explode(',', $request->employee_id);
        if (count($leader_id) > 0) {
            foreach ($leader_id as $ld) {

                $check = DB::table('leader_employees')
                    ->select('id')
                    ->where('Leader_id', $ld)
                    ->get();
                if (count($check) > 0) {
                    foreach ($check as $ck) {
                        $del = LeaderEmployee::find($ck->id);
                        if ($del != null) {
                            $del->delete();
                        }

                    }

                }

                if (count($employee_id) > 0) {
                    foreach ($employee_id as $ei) {
                        $insert = new LeaderEmployee();
                        $insert->Leader_id = $ld;
                        $insert->employee_id = $ei;
                        $insert->save();



                    }
                }
            }
            return response()->json([
                'message' => 'Team Updated successfully!'
            ], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaderEmployee $leaderEmployee)
    {
        //
    }
    public function teamData()
    {
        $employee = DB::table('users')
            ->select('id', 'username')
            ->where('role', '!=', 1)
            ->where('status', '1')
            ->get();

        return response()->json([
            'employees' => $employee,
        ]);
    }
    public function teamDatatable()
    {
        $collection = DB::table('leader_employees as le')
            ->join('users as u', 'u.id', '=', 'le.Leader_id')
            ->select('u.username as name', 'le.Leader_id as lid')

            ->distinct('le.Leader_id')
            ->get();

        return response()->json([

            'leaders' => $collection,
        ], 200);

    }
    public function teamDelete($id)
    {
        $delInfo = DB::table('leader_employees')
            ->select('id')
            ->where('Leader_id', $id)
            ->get();
        foreach ($delInfo as $di) {
            $delete = LeaderEmployee::find($di->id);
            $delete->delete();

        }
        return response()->json([

            'message' => "Team deleted Successfully",
        ], 200);

    }

    public function teamEdit($id)
    {
        $info = DB::table('leader_employees as le')
            ->join('users as u', 'u.id', '=', 'le.employee_id')
            ->select('u.username as name', 'le.employee_id as eid')
            ->where('le.Leader_id', $id)
            ->get();

        $employee = DB::table('users')
            ->select('id', 'username')
            ->where('role', '!=', 1)
            ->where('status', '1')
            ->get();



        $leader = DB::table('users')
            ->select('username', 'id')
            ->where('id', $id)
            ->get();

        return response()->json([
            'info' => $info,
            'leader' => $leader,
            'employee' => $employee
        ]);
    }
}
