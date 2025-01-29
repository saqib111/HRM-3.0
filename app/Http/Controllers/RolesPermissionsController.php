<?php

namespace App\Http\Controllers;

use App\Models\RolesPermissions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesPermissionsController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RolesPermissions $rolesPermissions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RolesPermissions $rolesPermissions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RolesPermissions $rolesPermissions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RolesPermissions $rolesPermissions)
    {
        //
    }

    public function getUserPermissions($userId)
    {
        $userPermissions = DB::table('user_permissions')->where('user_id', $userId)->first();

        $permissions = $userPermissions ? explode(',', $userPermissions->permissions) : [];

        return response()->json([
            'permissions' => $permissions,
        ]);
    }

    public function saveUserPermissions(Request $request, $userId)
    {

        $AuthID = auth()->user();
        Log::channel('permissions')->info("Permissions Updated By: User ID: {$AuthID->id}, Username: {$AuthID->username}, Employee ID: {$AuthID->employee_id}");

        $getuser = User::find($userId);

        // Log the action using the 'permissions' log channel
        Log::channel('permissions')->info("Permissions Updated For: User ID: {$getuser->id}, Username: {$getuser->username}, Employee ID: {$getuser->employee_id}");

        $permissions = $request->input('permissions'); // Array of permissions

        // Fetch the old permissions for logging purposes
        $oldPermissions = DB::table('user_permissions')->where('user_id', $userId)->value('permissions');

        // If old permissions are not found, log it as empty
        if (is_null($oldPermissions) || $oldPermissions === '') {
            $oldPermissions = 'No previous permissions found';
        }

        DB::table('user_permissions')->updateOrInsert(
            ['user_id' => $userId],
            [
                'permissions' => implode(',', $permissions),
                'updated_at' => now(),
                // If this is a new record, set the created_at timestamp
                'created_at' => $oldPermissions === 'No previous permissions found' ? now() : DB::raw('created_at')
            ]
        );

        // Prepare the new permissions
        $newPermissions = implode(',', $permissions);

        Log::channel('permissions')->info("Old Permissions: {$oldPermissions}");
        Log::channel('permissions')->info("New Permissions: {$newPermissions}");
        Log::channel('permissions')->info("Permissions updated successfully at " . now() . "\n");

        return response()->json(['message' => 'Permissions updated successfully.']);
    }

}