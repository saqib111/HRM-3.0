<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Models\VisaInfo;
use App\Models\User;
use App\Models\Emergency;
use App\Models\Dependant;
use DB;
use Yajra\DataTables\Facades\DataTables;


class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     */

    public function index(Request $request)
    {
        // Default user ID if not provided
        $userId = $request->input('user_id') ?? auth()->id(); // If user ID is provided, use it; otherwise, default to logged-in user
        // $userId = Auth::id();

        // Fetch profile users and related information
        $profileUsers = DB::table('users')
            ->join('visa_infos', 'users.id', '=', 'visa_infos.user_id')
            ->leftJoin('designations', 'designations.id', '=', 'users.designation_id')
            ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
            ->leftJoin('emergencies', 'emergencies.user_id', '=', 'users.id')
            ->leftJoin('dependants', 'dependants.user_id', '=', 'users.id')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select(
                'users.*',
                'visa_infos.*',
                'designations.name as designationName',
                'departments.name as departmentName',
                'user_profiles.*',
                'emergencies.*',
                'dependants.*'
            )
            ->where('users.id', $userId)
            ->get();

        // If the request is AJAX, return JSON response
        if ($request->ajax()) {
            return response()->json($profileUsers);
        }

        // Fetch visa information for the specific user
        $visaInfo = optional(DB::table('visa_infos')->where('user_id', $userId)->first());

        return view('usersProfile.user-profile', compact('profileUsers', 'visaInfo'));
    }


    public function allEmployee(Request $request)
    {
        if ($request->ajax()) {
            $profileUsers = User::select(
                'users.id',
                'users.employee_id',
                'visa_infos.visa_no as visa_no',
                'visa_infos.passport_no as passport_no',
                'user_profiles.real_name as real_name',
                'user_profiles.nationality as nationality',
                'companies.name as company_name',
                'users.email'
            )
                ->leftJoin('visa_infos', 'users.id', '=', 'visa_infos.user_id')
                ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id');

            return DataTables::of($profileUsers)
                ->addIndexColumn() // Automatically adds an index column
                // Make employee_id searchable
                ->filterColumn('employee_id', function ($query, $keyword) {
                    $query->where('users.employee_id', 'LIKE', "%{$keyword}%");
                })
                // Make real_name searchable
                ->filterColumn('real_name', function ($query, $keyword) {
                    $query->where('user_profiles.real_name', 'LIKE', "%{$keyword}%");
                })
                // Make email searchable
                ->filterColumn('email', function ($query, $keyword) {
                    $query->where('users.email', 'LIKE', "%{$keyword}%");
                })
                // Make company_name searchable
                ->filterColumn('company_name', function ($query, $keyword) {
                    $query->where('companies.name', 'LIKE', "%{$keyword}%");
                })
                // Make visa_no searchable
                ->filterColumn('visa_no', function ($query, $keyword) {
                    $query->where('visa_infos.visa_no', 'LIKE', "%{$keyword}%");
                })
                // Make passport_no searchable
                ->filterColumn('passport_no', function ($query, $keyword) {
                    $query->where('visa_infos.passport_no', 'LIKE', "%{$keyword}%");
                })
                // Make nationality searchable
                ->filterColumn('nationality', function ($query, $keyword) {
                    $query->where('user_profiles.nationality', 'LIKE', "%{$keyword}%");
                })
                ->rawColumns(['action'])
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

    public function profileShow($id)
    {
        $userProfile = User::with(['visaInfo', 'userProfile', 'emergencyUser', 'dependantUser'])->findOrFail($id);
        // $mainUser->week_days = $request->week_days;
        $mainUser = $userProfile;
        $visaInfo = $userProfile->visaInfo;
        $profileUser = $userProfile->userProfile;
        $EmergencyUser = $userProfile->emergencyUser;
        $DependantUser = $userProfile->dependantUser;

        return view('usersProfile.user-profile', compact('mainUser', 'profileUser', 'visaInfo', 'EmergencyUser', 'DependantUser'));
    }


    public function edit(UserProfile $userProfile)
    {
        $profileUser = DB::table('users')
            ->join('visa_infos', 'users.id', '=', 'visa_infos.user_id')
            ->leftJoin('designations', 'designations.id', '=', 'users.designation_id')
            ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select('users.*', 'visa_infos.*', 'designations.id as designation_id', 'designations.name as designationName', 'departments.name as departmentName', 'user_profiles.*')
            ->where('users.id', $userProfile->user_id)
            ->first();

        $designations = DB::table('designations')->select('id', 'name')->get();

        if ($designations->isEmpty()) {
            dd('No designations found.');
        }

        return view('user-edit', compact('profileUser', 'designations'));
    }






    public function update(Request $request, $id)
    {

        $mainUser = User::find($id);
        $mainUser->week_days = $request->week_days;
        $mainUser->save();

        $updatePersonal = UserProfile::where('user_id', $id)->first();


        $updatePersonal->real_name = $request->real_name;
        $updatePersonal->dob = $request->dob;
        $updatePersonal->accomodation = $request->accomodation;
        $updatePersonal->gender = $request->gender;
        $updatePersonal->phone = $request->phone;
        $updatePersonal->nationality = $request->nationality;
        $updatePersonal->telegram = $request->telegram;
        $updatePersonal->office = $request->office;
        $updatePersonal->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully!',
            'data' => [
                'real_name' => $updatePersonal->real_name,
                'dob' => $updatePersonal->dob,
                'accomodation' => $updatePersonal->accomodation,
                'gender' => $updatePersonal->gender,
                'phone' => $updatePersonal->phone,
                'nationality' => $updatePersonal->nationality,
                'telegram' => $updatePersonal->telegram,
                'office' => $updatePersonal->office,
                'week_days' => $mainUser->week_days,
            ]
        ]);
    }





    public function updateVisaInfo(Request $request)
    {
        // Find the visa information
        $visaInfo = VisaInfo::find($request->input('userProfileId'));

        if (!$visaInfo) {
            return response()->json(['message' => 'Visa information not found.'], 404);
        }

        // Update the visa information in the database
        $visaInfo->update($request->only([
            'passport_no',
            'p_issue_date',
            'p_expiry_date',
            'visa_no',
            'v_issue_date',
            'v_expiry_date',
            'foreign_no',
            'f_expiry_date',
        ]));

        // Return the updated information as JSON
        return response()->json([
            'visaInfo' => $visaInfo,
        ]);
    }


    //Emergency Update

    public function updateEmergency(Request $request)
    {
        // Find the emergency contact associated with the user ID
        $emergency = Emergency::where('user_id', $request->emergencyProfileId)->first();

        // If the record exists, update it; otherwise, create a new one
        if ($emergency) {
            $emergency->e_name = $request->emergency_name;
            $emergency->e_phone = $request->emergency_phone;
            $emergency->e_email = $request->emergency_email;
            $emergency->e_address = $request->emergency_address;
            $emergency->e_country = $request->emergency_country;
            $emergency->e_gender = $request->emergency_gender;
            $emergency->e_relationship = $request->emergency_relation;
            $emergency->save();

            return response()->json([
                'status' => 'success',
                'data' => $emergency,
            ]);
        }

        return redirect()->route('user-profile.index')->with('status', 'Emergency contact added successfully');
    }

    public function updatedependant(Request $request)
    {
        // Find the dependant by ID
        $dependant = dependant::findOrFail($request->dependantProfileId);

        // Update the dependant's attributes
        $dependant->d_name = $request->dependant_name;
        $dependant->d_gender = $request->dependant_gender;
        $dependant->d_nationality = $request->dependant_nationality;
        $dependant->d_dob = $request->dependant_dob;
        $dependant->d_passport_no = $request->dependant_passport_no;
        $dependant->d_pass_issue_date = $request->dependant_pass_issue_date;
        $dependant->d_pass_expiry_date = $request->dependant_pass_expiry_date;
        $dependant->d_visa_no = $request->dependant_visa_no;
        $dependant->d_visa_issue_date = $request->dependant_visa_issue_date;
        $dependant->d_visa_expiry_date = $request->dependant_visa_expiry_date;

        // Save the updated dependant
        $dependant->save();

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'dependant updated successfully!',
            'data' => $dependant,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(userProfile $userProfile)
    {
        //
    }
}