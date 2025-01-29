<?php

namespace App\Http\Controllers;

use App\Models\ManageIpRestriction;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageIpRestrictionsController extends Controller
{
    //
    public function view(Request $request)
    {

        $this->populateTable();

        return view("whitelistIPs.manage_ip_restrictions");

    }

    public function getUsersData()
    {
        $users_data = ManageIpRestriction::select(
            'manage_ip_restrictions.id',
            'users.employee_id',
            'users.username',
            'users.email',
            'manage_ip_restrictions.status'
        )
            ->join('users', 'manage_ip_restrictions.user_id', '=', 'users.id') // Join users table
            ->orderBy('users.employee_id', 'asc')
            ->get();

        return DataTables::of($users_data)
            ->addColumn('employee_id', function ($row) {
                return $row->employee_id; // Fetch directly from query result
            })
            ->addColumn('username', function ($row) {
                return $row->username; // Fetch directly from query result
            })
            ->addColumn('email', function ($row) {
                return $row->email; // Fetch directly from query result
            })
            ->addColumn('status', function ($row) {
                return $row->status; // Fetch directly from query result
            })
            ->make(true);
    }



    public function updateStatus(Request $request)
    {
        $authId = auth()->user();
        $serverTime = now();

        $restriction = ManageIpRestriction::findOrFail($request->id);

        $user = $restriction->user;
        $username = $user->username;
        $employeeId = $user->employee_id;

        // Log the initial status before updating
        $oldStatus = $restriction->status == 1 ? 'Allowed' : 'Restricted';

        $restriction->status = $request->status;
        $newStatus = $restriction->status == 1 ? 'Allowed' : 'Restricted';

        $restriction->save();

        // Log the update action
        Log::channel('ip_restriction')->info("Updated By: User ID: {$authId->id} - Username:{$authId->username} - Employee ID: {$authId->employee_id}");
        Log::channel('ip_restriction')->info("Updated status for ID: {$restriction->id} => Username: {$username} (Employee ID: {$employeeId}) \nOld status: {$oldStatus} - New status: {$newStatus}");
        Log::channel('ip_restriction')->info("Timestamp: {$serverTime}\n");


        // Return the updated status to the client
        return response()->json([
            'status' => $restriction->status, // Return the updated status (1 or 0)
        ]);
    }

    // *********** TO STORE THE DATA IN THE TABLE **********
    public function populateTable()
    {

        if (ManageIPRestriction::count() === 0) {
            $users = User::all();

            foreach ($users as $user) {
                ManageIpRestriction::create([
                    'user_id' => $user->id,
                    'status' => 1, // 0 = RESTRICTED, 1 = ALLOWED.  DEFAULT( 0 = RESTRICTED )
                ]);
            }

        }

    }

}