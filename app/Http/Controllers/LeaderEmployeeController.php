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
        $authId = auth()->user();
        $serverTime = now();

        // Get the leader_id and employee_id as arrays
        $leader_id = explode(',', $request->leader_id);
        $employee_id = explode(',', $request->employee_id);

        // Log the incoming request data
        $logger = app('log')->channel('team_info');
        $logger->info("Team Created By: User ID: {$authId->id}, Username:{$authId->username}, Employee ID:{$authId->employee_id}");


        // Retrieve leader usernames using DB
        $leaderUsernames = DB::table('users')->whereIn('id', $leader_id)->pluck('username', 'id')->toArray();

        // Retrieve employee usernames using DB
        $employeeUsernames = DB::table('users')->whereIn('id', $employee_id)->pluck('username', 'id')->toArray();

        // Log the usernames of the leaders and employees
        $leaderUsernamesLog = implode(', ', array_map(fn($id) => $leaderUsernames[$id], $leader_id));
        $employeeUsernamesLog = implode(', ', array_map(fn($id) => $employeeUsernames[$id], $employee_id));

        $logger->info("Leader Name: {$leaderUsernamesLog}");
        $logger->info("Employee Names: {$employeeUsernamesLog}");


        // Check if any of the leaders already exist in the leader_employees table
        $existingLeaders = DB::table('leader_employees')
            ->whereIn('Leader_id', $leader_id)
            ->pluck('Leader_id'); // Get the ids of leaders that already exist

        // If any of the leaders already exist, return an error response
        if ($existingLeaders->count() > 0) {
            $logger->warning('Conflict detected! Leader(s) already exist: ' . implode(',', $existingLeaders->toArray()) . "\n");
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

        $logger->info("Timestamp: {$serverTime}");
        // Return success message after saving
        $logger->info("Team created successfully!\n");
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
        $authId = auth()->user();
        $serverTime = now();

        $leader_id = explode(',', $request->leader_id);
        $employee_id = explode(',', $request->employee_id);

        // Log the incoming request data
        $logger = app('log')->channel('team_info');
        $logger->info("Team Updated By: User ID: {$authId->id}, Username: {$authId->username}, Employee ID: {$authId->employee_id}");
        // Get the usernames for the leader_id array
        $leaderUsernames = DB::table('users')->whereIn('id', $leader_id)->pluck('username', 'id');
        $leaderUsernamesList = $leaderUsernames->only($leader_id)->implode(',', $leaderUsernames->toArray());

        // Log the leader usernames
        $logger->info("Leader Name: " . $leaderUsernamesList);


        if (count($leader_id) > 0) {
            foreach ($leader_id as $ld) {

                // Select Leader_id, employee_id, and id to log the old records
                $check = DB::table('leader_employees')
                    ->select('id', 'Leader_id', 'employee_id')  // Include 'Leader_id' and 'employee_id' in the select
                    ->where('Leader_id', $ld)
                    ->get();

                // Collect employee IDs from old records
                $oldEmployeeIds = $check->pluck('employee_id')->toArray();  // Get the employee IDs from the old records

                // Get usernames for old employee_ids
                $oldEmployeeUsernames = DB::table('users')->whereIn('id', $oldEmployeeIds)->pluck('username', 'id');
                $oldEmployeeUsernamesList = $oldEmployeeUsernames->only($oldEmployeeIds)->implode(', ', $oldEmployeeUsernames->toArray());

                // Log the old employee usernames as comma-separated
                if (!empty($oldEmployeeUsernamesList)) {
                    $logger->info("Old Record Employee Usernames: " . $oldEmployeeUsernamesList);
                }

                // Log the old records
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

                        // Get the username for the new employee
                        $employeeUsername = DB::table('users')->where('id', $ei)->value('username');
                        $newEmployeeUsernames[] = $employeeUsername; // Collecting employee usernames

                    }
                }
            }


            // Log all updated Employee Usernames after insertion
            $logger->info("Updated Employee Usernames: " . implode(', ', $newEmployeeUsernames));
            $logger->info("Timestamp: {$serverTime}");
            $logger->info("Team Updated Successfully. \n");

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
    public function teamData(Request $request)
    {
        $id = auth()->user()->id;
        if (auth()->user()->role == "1") {
            $query = DB::table('users as u')
                ->join('departments as d', 'u.department_id', '=', 'd.id')
                ->select('u.id as id', 'u.employee_id as employee_id', 'u.username as username', 'd.name as department');

        } else {
            $query = DB::table('users as u')
                ->join('departments as d', 'u.department_id', '=', 'd.id')
                ->join('leader_employees as ld', 'ld.employee_id', '=', 'u.id')
                ->select('u.id as id', 'u.employee_id as employee_id', 'u.username as username', 'd.name as department')
                ->where('ld.leader_id', '=', $id);
        }

        if ($request->has('search') && $request->input('search')['value']) {
            $search = $request->input('search')['value'];
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        }



        $perPage = $request->input('length', 10);
        $currentPage = ($request->input('start', 0) / $perPage) + 1;

        $users = $query->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $users->items(),
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
            ->filterColumn('name', function ($query, $keyword) {  // Change filter to use the correct column
                // Filter based on the 'u.username' which is aliased as 'name'
                $query->where('u.username', 'LIKE', "%{$keyword}%");
            })
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
        // Get the authenticated user details
        $authId = auth()->user();
        $serverTime = now();

        // Get Leader details (username and employee_id) based on Leader_id
        $leaderDetails = DB::table('users')->select('id', 'username', 'employee_id')->where('id', $id)->first();

        // Log the deletion request
        $logger = app('log')->channel('team_info');
        $logger->info("Team Deletion Requested By: User ID: {$authId->id}, Username: {$authId->username}, Employee ID: {$authId->employee_id}");
        $logger->info("Deleting Team with Leader ID: {$leaderDetails->id}, Username: {$leaderDetails->username}, Employee ID: {$leaderDetails->employee_id}");

        $delInfo = DB::table('leader_employees')
            ->select('id')
            ->where('Leader_id', $id)
            ->get();
        foreach ($delInfo as $di) {
            $delete = LeaderEmployee::find($di->id);
            $delete->delete();

        }

        // Log the success message and the time the operation was performed
        $logger->info("Timestamp: {$serverTime}");
        $logger->info("Team Deletion Completed Successfully. \n");
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
