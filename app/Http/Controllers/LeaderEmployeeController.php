<?php

namespace App\Http\Controllers;

use App\Models\LeaderEmployee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
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
        // Get the leader_id and employee_id as arrays
        $leader_id = explode(',', $request->leader_id);
        $employee_id = explode(',', $request->employee_id);

        // Check if any of the leaders already exist in the leader_employees table
        $existingLeaders = DB::table('leader_employees')
            ->whereIn('Leader_id', $leader_id)
            ->pluck('Leader_id'); // Get the ids of leaders that already exist

        // If any of the leaders already exist, return an error response
        if ($existingLeaders->count() > 0) {
            return response()->json([
                'message' => 'Team Leader already exists!'
            ], 409); // Conflict status code
        }

        // Proceed with saving the new leaders and employees (remove this part if you only need to check leader existence)
        foreach ($leader_id as $ld) {
            foreach ($employee_id as $ei) {
                $insert = new LeaderEmployee();
                $insert->Leader_id = $ld;
                $insert->employee_id = $ei;
                $insert->save();
            }
        }

        // Return success message after saving
        return response()->json([
            'message' => 'Team created successfully!'
        ], 200);
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
            ->where('status', '1')
            ->get();

        return response()->json([
            'employees' => $employee,
        ]);
    }
    public function teamDatatable()
    {
        $user = auth()->user();

        // Fetch user permissions
        $permissions = getUserPermissions($user);
        $canUpdateTeam = $user->role == 1 || in_array('update_team', $permissions);
        $canDeleteTeam = $user->role == 1 || in_array('delete_team', $permissions);

        // Get data from the database
        $collection = DB::table('leader_employees as le')
            ->join('users as u', 'u.id', '=', 'le.Leader_id')
            ->join('users as emp', 'emp.id', '=', 'le.employee_id')
            ->select(
                'u.username as name',
                'le.Leader_id as lid',
                DB::raw('GROUP_CONCAT(emp.username SEPARATOR ", ") as employee_names')
            )
            ->groupBy('le.Leader_id', 'u.username')
            ->orderBy('le.Leader_id', 'asc');

        return DataTables::of($collection)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($canUpdateTeam, $canDeleteTeam) {
                return [
                    'edit' => $canUpdateTeam,
                    'delete' => $canDeleteTeam,
                ];
            })
            ->make(true);
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
