<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return view('user-profile');
        $profileUsers = DB::table('users')
            ->join('visa_infos', 'users.id', '=', 'visa_infos.user_id')
            ->leftJoin('designations', 'designations.id', 'users.designation_id')
            ->leftJoin('departments', 'departments.id', 'users.department_id')
            ->Join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select('users.*', 'visa_infos.*', 'designations.name as designationName', 'departments.name as departmentName', 'user_profiles.*')
            ->where('users.id', 6) // Add the condition to filter by user ID
            ->get();
        return view('user-profile', compact('profileUsers'));
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
    public function show(userProfile $userProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(userProfile $userProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, userProfile $userProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(userProfile $userProfile)
    {
        //
    }
}