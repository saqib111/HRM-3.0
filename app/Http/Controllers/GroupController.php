<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\{
    DB,
    Session,
    Http
};
use App\Models\{

    User,
    LeaderEmployee,
    AttendanceRecord,
};

class GroupController extends Controller
{
    /** 
     * Display a listing of the resource. 
     */
    public function index()
    {



        return view('schedule.group');
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
        try {
            $id = auth()->user()->id;


            $group = new Group();
            $group->name = $request->group_name;
            $group->user_id = $request->selectedEmployee;
            $group->leader_id = $id;
            $group->status = "0";
            $group->save();

            return response()->json([
                'message' => 'Group created successfully!'
            ], 200);



        } catch (\Exception $e) {

            $e->getMessage();

        }

    }

    /** 
     * Display the specified resource. 
     */
    public function show(Group $group)
    {
        // 
    }

    /** 
     * Show the form for editing the specified resource. 
     */
    public function edit(Group $group)
    {
        // 
    }

    /** 
     * Update the specified resource in storage. 
     */
    public function update(Request $request, Group $group)
    {
        // 
    }

    /** 
     * Remove the specified resource from storage. 
     */
    public function destroy($id)
    {
        $group = Group::find($id);
        $group->delete();
        return response()->json([
            'message' => 'Group delted successfully!'
        ], 200);
    }
    public function groupData(Request $request)
    {
        $id = auth()->user()->id;
        // $employee=DB::table('users as u') 
        //       ->join('leader_employees as lu','u.id','=','lu.employee_id') 
        //       ->where('lu.Leader_id',$id) 
        //       ->where('lu.status','0') 
        //       ->select('u.id as id','u.username as name') 
        //       ->get(); 
        if (auth()->user()->role == "1") {
            $group = Group::where('status', '0');
        } else {
            $group = Group::where('leader_id', $id)->where('status', '0');
        }


        if ($request->has('name')) {
            $group->where('name', $request->input('group_name'));
        }

        return DataTables::of($group)
            ->addIndexColumn()
            // Make company_name searchable using the "whereHas" filter for joined fields 


            ->filterColumn('Group Name', function ($query, $keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%");

            })

            ->addColumn('action', function ($row) {
                $btn = '<button class="btn btn-danger" onclick="deleSchedule(' . $row->id . ')"><i class="fa fa-trash"></i></button>';
                $btn = '<button class="btn btn-danger" onclick="editSchedule(' . $row->id . ')"><i class="fa fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function groupEmployee()
    {
        $id = auth()->user()->id;
        $employee = DB::table('users as u')
            ->join('leader_employees as lu', 'u.id', '=', 'lu.employee_id')
            ->where('lu.Leader_id', $id)
            ->where('lu.status', '0')
            ->select('u.id as id', 'u.username as name')
            ->get();
        return response()->json([
            'employee' => $employee,
        ]);
    }

    public function groupChange()
    {
        $id = auth()->user()->id;

        $groups = DB::table('groups')
            ->select('*')
            ->where('leader_id', $id)
            ->where('status', '0')
            ->get();


        return view('schedule.group-change', compact('groups'));
    }
    public function changeGroupData(Request $request)
    {

        if ($request->group_id == null) {
            $oldId = $request->old_group_id;
            $msg = "Select Group";

            return redirect()->back()->with('oldId', $oldId)->withErrors(['group' => ['select Group']]);
            ;
        }
        $old_id = $request->old_group_id;
        $employee_id = $request->employee_id;
        $group_id = $request->group_id;

        $old = DB::table('groups')
            ->select('user_id')
            ->where('id', $old_id)
            ->first();
        $oldEmployee = explode(',', $old->user_id);
        $pos = array_search($employee_id, $oldEmployee);
        if ($pos !== false) {
            unset($oldEmployee[$pos]);
        }
        $old_update = Group::find($old_id);
        $old_update->update([
            $old_update->user_id = implode(',', $oldEmployee),
        ]);

        $new = DB::table('groups')
            ->select('user_id')
            ->where('id', $group_id)
            ->first();
        $newEmployee = explode(',', $new->user_id);
        $check = array_search($employee_id, $newEmployee);
        if ($check == false) {
            array_push($newEmployee, $employee_id);
        }
        $newUpdate = Group::find($group_id);
        $newUpdate->update([
            $newUpdate->user_id = implode(',', $newEmployee),

        ]);
        return redirect()->back()->with('success', 'Employe group has been changed Successfully');
    }

    public function groupMember($id)
    {
        $data = DB::table('groups')
            ->select('name', 'user_id')
            ->where('id', $id)
            ->first();
        $name = [];
        $username = explode(',', $data->user_id);
        for ($i = 0; $i < count($username); $i++) {
            $nam = getName($username[$i]);
            array_push($name, $nam);
        }
        return (['name' => $name, 'total' => count($username), 'group_name' => $data->name]);
    }

    // public function getTeamMember(Request $request)
    // {
    //     // Get the search term and page number
    //     $searchTerm = $request->input('searchTerm', '');
    //     $page = $request->input('page', 1);

    //     // Get the authenticated user's ID and role
    //     $userId = auth()->id();
    //     $userRole = auth()->user()->role;  // Assuming 'role' is a column in the users table

    //     // Initialize the query for fetching users
    //     $usersQuery = User::query();

    //     // Check the user's role
    //     if ($userRole == 1) {
    //         // If role is 1, show all users
    //         $usersQuery->select('id', 'username');
    //     } else {
    //         // If role is 2, 3, 4, or 5, show only the team members based on the leader_employees table
    //         $usersQuery->join('leader_employees', 'leader_employees.employee_id', '=', 'users.id')
    //             ->where('leader_employees.leader_id', $userId);
    //     }

    //     // Apply search filter if a search term is provided
    //     if ($searchTerm) {
    //         $usersQuery->where('users.username', 'like', '%' . $searchTerm . '%');
    //     }

    //     // Paginate the results (10 items per page) and select only 'id' and 'username' columns
    //     $users = $usersQuery->paginate(10, ['users.id', 'users.username'], 'page', $page);

    //     // Return the results as JSON
    //     return response()->json([
    //         'data' => $users->items(),   // Return the employees' data
    //         'last_page' => $users->lastPage(),
    //     ]);
    // }
    public function getTeamMember(Request $request)
    {
        // Get the search term and page number
        $searchTerm = $request->input('searchTerm', '');
        $page = $request->input('page', 1);

        // Get the authenticated user's ID and role
        $userId = auth()->id();
        $userRole = auth()->user()->role;  // Assuming 'role' is a column in the users table

        // Initialize the query for fetching users
        $usersQuery = User::query();

        // Check the user's role
        if ($userRole == 1) {
            // If role is 1, show all users
            $usersQuery->select('id', 'username');
        } elseif ($userRole == "2") {
            $usersQuery->select('id', 'username')
                ->whereIn("role", ["2", "4", "5"]);
        } else {
            // If role is 2, 3, 4, or 5, show only the team members based on the leader_employees table
            $usersQuery->join('leader_employees', 'leader_employees.employee_id', '=', 'users.id')
                ->where('leader_employees.leader_id', $userId);
        }

        // Apply search filter if a search term is provided
        if ($searchTerm) {
            $usersQuery->where('users.username', 'like', '%' . $searchTerm . '%');
        }

        // Paginate the results (10 items per page) and select only 'id' and 'username' columns
        $users = $usersQuery->paginate(10, ['users.id', 'users.username'], 'page', $page);

        // Return the results as JSON
        return response()->json([
            'data' => $users->items(),   // Return the employees' data
            'last_page' => $users->lastPage(),
        ]);
    }
}