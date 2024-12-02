<?php

namespace App\Http\Controllers;

use App\Models\RolesPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesPermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('roles-permissions');
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
        $permissions = $request->input('permissions'); // Array of permissions

        DB::table('user_permissions')->updateOrInsert(
            ['user_id' => $userId],
            ['permissions' => implode(',', $permissions)]
        );

        return response()->json(['message' => 'Permissions updated successfully.']);
    }

}