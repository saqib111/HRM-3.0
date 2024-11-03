<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\VisaInfo;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AllEmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $profileUser = User::select(
                'users.*',
                'visa_infos.visa_no as visa_no',
                'visa_infos.passport_no as passport_no',
                'user_profiles.real_name as real_name',
                'user_profiles.nationality as nationality',
                'companies.name as company_name'
            )
                ->leftJoin('visa_infos', 'users.id', '=', 'visa_infos.user_id')
                ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
                ->get();

            return DataTables::of($profileUser)
                ->addColumn('action', function ($row) {
                    return '<div class="text-center" style="cursor: pointer;" 
                    onmouseover="this.querySelector(\'i\').style.color=\'#0272D9\'" 
                    onmouseout="this.querySelector(\'i\').style.color=\'#000\'">
                    <a href="' . route('user-profile', ['id' => $row->id]) . '">
                        <i class="ion-eye" title="View" style="font-size: 24px; color: #000;"></i>
                    </a>
                    </div>';
                })
                ->make(true);
        }

        // Render the view for non-AJAX requests
        return view('usersProfile.all-employees');
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}