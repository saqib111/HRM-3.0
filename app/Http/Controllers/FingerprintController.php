<?php

namespace App\Http\Controllers;

use App\Models\Fingerprint;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class FingerprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Get and cast user_id(s) to an array (split by commas)
            $userIds = explode(',', $request->input('user_id')); // e.g., "1.2.3.4,5" => [1, 2, 3, 4, 5]

            // Get start and end date from the request
            $startDate = $request->input('start_date') ?? Carbon::now()->startOfMonth()->toDateString();
            $endDate = $request->input('end_date') ?? Carbon::now()->endOfMonth()->toDateString();

            // Adjust dates to cover the full day
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            // Fetch user data for all user IDs
            $users = User::whereIn('id', $userIds)->get();

            if ($users->isEmpty()) {
                return response()->json(['data' => []]); // Return empty data if no users are found
            }

            // Extract numeric part from employee_id (e.g., AHNV00315 -> 315)
            $employeeIds = [];
            foreach ($users as $user) {
                preg_match('/\d+$/', $user->employee_id, $matches);
                $employeeId = isset($matches[0]) ? (int) $matches[0] : null;
                if ($employeeId) {
                    $employeeIds[] = $employeeId;
                }
            }

            if (empty($employeeIds)) {
                return response()->json(['data' => []]); // Return empty data if no valid employee_ids found
            }

            // Fetch fingerprint records where the numeric employee_id matches any of the user_ids
            $data = Fingerprint::join('users', function ($join) {
                $join->on(DB::raw('CAST(SUBSTRING(users.employee_id, -5) AS UNSIGNED)'), '=', 'check_verify.user_id');
            })
                ->whereIn('check_verify.user_id', $employeeIds) // Use whereIn for multiple user IDs
                ->whereBetween('check_verify.fingerprint_in', [$startDate, $endDate])
                ->select(
                    'check_verify.id',
                    'check_verify.user_id',
                    'check_verify.type',
                    'check_verify.fingerprint_in',
                    'check_verify.last_processed_timestamp',
                    'users.employee_id',
                    'users.username',
                    'check_verify.created_at',
                    'check_verify.updated_at'
                )
                ->get();

            // Return the filtered data to DataTables
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        // Return the main view if it's not an AJAX request
        return view('fingerprint.fingerprint-record');
    }


    public function searchUsers(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);  // Get the current page, default to 1
        $allUsers = $request->input('all', false);  // Check if we want to fetch all users without pagination

        // Check if the user is an admin or not (role == 1)
        if (auth()->user()->role == 1 || auth()->user()->role == 2) {
            // If role is 1, fetch all active users matching the search
            $query = User::when($search, function ($query) use ($search) {
                return $query->where('username', 'LIKE', '%' . $search . '%');
            })
                ->where('status', '1');
        } else {
            // If role is not 1, fetch users from the team based on leader_employees table
            $leaderId = auth()->user()->id;  // Get the logged-in user's ID

            // Get the active users that belong to this leader (where leader_id matches)
            $teamMembersQuery = User::when($search, function ($query) use ($search) {
                return $query->where('username', 'LIKE', '%' . $search . '%');
            })
                ->where('status', '1')
                ->whereIn('id', function ($query) use ($leaderId) {
                    $query->select('employee_id')
                        ->from('leader_employees')
                        ->where('leader_id', $leaderId);  // Get team members
                });

            // Include the leader's own ID in the result set
            $leaderQuery = User::when($search, function ($query) use ($search) {
                return $query->where('username', 'LIKE', '%' . $search . '%');
            })
                ->where('status', '1')
                ->where('id', $leaderId);  // Include leader's own record

            // Union the leader's own data with the team members' data
            $query = $teamMembersQuery->union($leaderQuery);
        }

        // Determine whether to paginate or get all results
        if ($allUsers) {
            $data = $query->get();
            $response = [
                'data' => $data,
                'total' => $data->count(),
            ];
        } else {
            $data = $query->paginate(10, ['*'], 'page', $page);
            $response = [
                'data' => $data->items(),
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ];
        }

        // Check if no data was found
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No users found',
                'data' => [],
                'total' => 0,
                'current_page' => $allUsers ? null : $data->currentPage(),
                'last_page' => $allUsers ? null : $data->lastPage(),
            ]);
        }

        return response()->json($response);
    }

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
    public function show($id)
    {

        // $user = User::find($id);
        $user = User::whereRaw('CAST(SUBSTRING(employee_id, -5) AS UNSIGNED) = ?', [$id])->first();
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Fetch the fingerprint record by ID
        $fingerprint = Fingerprint::findOrFail($id);

        // Return the fingerprint data as a JSON response (for AJAX)
        return response()->json([
            'fingerprint' => $fingerprint
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'type' => 'required|string|max:255',
        ]);

        // Find the fingerprint record
        $fingerprint = Fingerprint::findOrFail($id);

        // Update the record
        $fingerprint->update([
            'type' => $request->input('type'),
        ]);

        // Return a success response with the updated fingerprint
        return response()->json([
            'success' => true,
            'message' => 'Fingerprint updated successfully!',
            'fingerprint' => $fingerprint,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    // In your FingerprintRecordController
    public function destroy($id)
    {
        // Find the fingerprint record by ID
        $fingerprint = Fingerprint::find($id);

        // Check if the record exists
        if ($fingerprint) {
            // Delete the fingerprint record
            $fingerprint->delete();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Fingerprint record deleted successfully.'
            ]);
        } else {
            // Return an error response if the record was not found
            return response()->json([
                'success' => false,
                'message' => 'Fingerprint record not found.'
            ]);
        }
    }

}
