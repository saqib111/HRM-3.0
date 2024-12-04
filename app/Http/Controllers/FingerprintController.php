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
            $userId = $request->input('user_id');  // Get the user_id from the request
            $userId = (int) $userId;  // Cast userId to an integer for proper comparison

            // Get start and end date from the request
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // If no date range is provided, set the date range to the current month
            if (!$startDate || !$endDate) {
                $startDate = Carbon::now()->startOfMonth()->toDateString();
                $endDate = Carbon::now()->endOfMonth()->toDateString();
            }

            // Make sure the start date is at the beginning of the day (00:00:01)
            $startDate = Carbon::parse($startDate)->startOfDay()->addSecond();

            // Make sure the end date is at the end of the day (23:59:59)
            $endDate = Carbon::parse($endDate)->endOfDay();  // This will set it to 23:59:59

            // dd($startDate, $endDate);

            // Fetch user data using the user_id (which corresponds to users.id)
            $usersData = User::where('id', '=', $userId)->first();
            if (!$usersData) {
                return response()->json(['data' => []]); // If no user found, return empty data
            }

            // Extract the numeric part from the employee_id (e.g., AHNV00315 -> 315)
            $employee_ID = $usersData->employee_id;
            preg_match('/[1-9][0-9]*/', $employee_ID, $actual_id);
            $employee_id = (int) $actual_id[0]; // Cast to integer

            // Fetch fingerprint records where user_id in check_verify matches the numeric part of employee_id
            $data = Fingerprint::join('users', function ($join) use ($employee_id) {
                $join->on(DB::raw('CAST(SUBSTRING(users.employee_id, -5) AS UNSIGNED)'), '=', DB::raw('?'))
                    ->addBinding($employee_id, 'select');
            })
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
                ->whereBetween('check_verify.fingerprint_in', [$startDate, $endDate])  // Filter by date range
                ->get();

            // Return the filtered data to DataTables
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        // Return the main view if it's not an AJAX request
        return view('fingerprint.fingerprint-record');
    }


    // public function searchUsers(Request $request)
    // {
    //     $search = $request->input('search');
    //     $page = $request->input('page', 1);  // Get the current page, default to 1

    //     // Check if the user is an admin or not (role == 1)
    //     if (auth()->user()->role == 1) {
    //         // If role is 1, return all active users
    //         $data = User::when($search, function ($query) use ($search) {
    //             return $query->where('username', 'LIKE', '%' . $search . '%');
    //         })
    //             ->where('status', '1')
    //             ->paginate(10, ['*'], 'page', $page);
    //     } else {
    //         // If role is not 1, fetch users from the team based on leader_employees table
    //         $leaderId = auth()->user()->id;  // Get the logged-in user's ID

    //         // Get the active users that belong to this leader (where leader_id matches)
    //         $data = User::when($search, function ($query) use ($search) {
    //             return $query->where('username', 'LIKE', '%' . $search . '%');
    //         })
    //             ->where('status', '1')
    //             ->whereIn('id', function ($query) use ($leaderId) {
    //                 $query->select('employee_id')
    //                     ->from('leader_employees')
    //                     ->where('leader_id', $leaderId);  // Filter by the logged-in user's leader_id
    //             })
    //             ->paginate(10, ['*'], 'page', $page);
    //     }

    //     return response()->json([
    //         'data' => $data->items(),
    //         'total' => $data->total(),
    //         'current_page' => $data->currentPage(),
    //         'last_page' => $data->lastPage(),
    //     ]);
    // }



    public function searchUsers(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);  // Get the current page, default to 1

        // Check if the user is an admin or not (role == 1)
        if (auth()->user()->role == 1) {
            // If role is 1, return all active users
            $data = User::when($search, function ($query) use ($search) {
                return $query->where('username', 'LIKE', '%' . $search . '%');
            })
                ->where('status', '1')
                ->paginate(10, ['*'], 'page', $page);
        } else {
            // If role is not 1, fetch users from the team based on leader_employees table
            $leaderId = auth()->user()->id;  // Get the logged-in user's ID

            // Get the active users that belong to this leader (where leader_id matches)
            $data = User::when($search, function ($query) use ($search) {
                return $query->where('username', 'LIKE', '%' . $search . '%');
            })
                ->where('status', '1')
                ->whereIn('id', function ($query) use ($leaderId) {
                    $query->select('employee_id')
                        ->from('leader_employees')
                        ->where('leader_id', $leaderId);  // Filter by the logged-in user's leader_id
                })
                ->paginate(10, ['*'], 'page', $page);
        }

        // Check if no data was found
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No users found',
                'data' => [],
                'total' => 0,
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ]);
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
