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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $mainUser = $userProfile;
        $visaInfo = $userProfile->visaInfo;
        $profileUser = $userProfile->userProfile;
        $EmergencyUser = $userProfile->emergencyUser;
        $DependantUser = $userProfile->dependantUser;

        $allowedUlArray = explode(',', $userProfile->allowed_ul);

        return view('usersProfile.user-profile', compact('mainUser', 'profileUser', 'visaInfo', 'EmergencyUser', 'DependantUser', 'allowedUlArray'));
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
        $authId = auth()->user();
        $serverTime = now();
        $mainUser = User::find($id);

        // Capture old week_days before updating
        $oldWeekDays = $mainUser->week_days;

        $mainUser->week_days = $request->week_days;
        $mainUser->save();

        // Log initial update action
        Log::channel('employee_info')->info("Updated By: User ID: {$authId->id}, Username:{$authId->username}, Employee ID:{$authId->employee_id}");
        Log::channel('employee_info')->info("Updated For: User ID: {$id}, Username:{$mainUser->username}, Employee ID:{$mainUser->employee_id}");
        // Log the user data update
        Log::channel('employee_info')->info("Old Week Days:{$oldWeekDays} \nUpdated Week Days: {$mainUser->week_days}");

        $updatePersonal = UserProfile::where('user_id', $id)->first();

        // Capture old user profile data before updating
        $oldProfile = $updatePersonal->replicate();

        $updatePersonal->real_name = $request->real_name;
        $updatePersonal->dob = $request->dob;
        $updatePersonal->accomodation = $request->accomodation;
        $updatePersonal->gender = $request->gender;
        $updatePersonal->phone = $request->phone;
        $updatePersonal->nationality = $request->nationality;
        $updatePersonal->telegram = $request->telegram;
        $updatePersonal->office = $request->office;


        // Format the old and new data for logging
        $oldFormatted = "
        Old Data:
        Real Name: {$oldProfile->real_name}
        DateOfBirth: " . Carbon::parse($oldProfile->dob)->format('d-M-Y') . "
        Accomodation: {$oldProfile->accomodation}
        Gender: {$oldProfile->gender}
        Phone: {$oldProfile->phone}
        Nationality: {$oldProfile->nationality}
        Telegram: {$oldProfile->telegram}
        Office: {$oldProfile->office}
        ";

        $newFormatted = "
        New Data:
        Real Name: {$updatePersonal->real_name}
        DateOfBirth: " . Carbon::parse($updatePersonal->dob)->format('d-M-Y') . "
        Accomodation: {$updatePersonal->accomodation}
        Gender: {$updatePersonal->gender}
        Phone: {$updatePersonal->phone}
        Nationality: {$updatePersonal->nationality}
        Telegram: {$updatePersonal->telegram}
        Office: {$updatePersonal->office}
        ";

        // Log the formatted data
        Log::channel('employee_info')->info("Personal Information:\n" . $oldFormatted . "\n" . $newFormatted);

        // Map numeric leave types to corresponding string labels
        $allowedUlMapping = [
            4 => 'UL',
            5 => 'HL',
            6 => 'CL',
            7 => 'MTL',
            8 => 'PL'
        ];

        // Capture old allowed_ul data before updating
        $oldAllowedUl = $updatePersonal->allowed_ul;

        if ($request->has('allowed_ul') && is_array($request->allowed_ul)) {
            $allowedUlString = implode(',', $request->allowed_ul);

            // Map the numeric leave types to their corresponding string labels
            $allowedUlText = array_map(function ($value) use ($allowedUlMapping) {
                return isset($allowedUlMapping[$value]) ? $allowedUlMapping[$value] : $value; // Fallback to the original value if no mapping is found
            }, $request->allowed_ul);

            // Convert the mapped array back to a string
            $allowedUlTextString = implode(',', $allowedUlText);

            $updatePersonal->allowed_ul = $allowedUlString;

            // Map old allowed_ul values to text
            if ($oldAllowedUl) {
                $oldAllowedUlArray = explode(',', $oldAllowedUl);
                $oldAllowedUlText = array_map(function ($value) use ($allowedUlMapping) {
                    return isset($allowedUlMapping[$value]) ? $allowedUlMapping[$value] : $value;
                }, $oldAllowedUlArray);

                $oldAllowedUlTextString = implode(',', $oldAllowedUlText);
            } else {
                $oldAllowedUlTextString = 'None';
            }

            // Log both old and new allowed_ul with mapped labels
            Log::channel('employee_info')->info("Old UnPaid Leaves (UL): {$oldAllowedUlTextString}");
            Log::channel('employee_info')->info("Updated UnPaid Leaves (UL): {$allowedUlTextString}");
            Log::channel('employee_info')->info("Timestamp: {$serverTime}");


        } else {

            // If allowed_ul is not present or empty, save null
            if ($oldAllowedUl) {
                // Ensure we log the old value if 'allowed_ul' is not being updated
                $oldAllowedUlArray = explode(',', $oldAllowedUl);
                $oldAllowedUlText = array_map(function ($value) use ($allowedUlMapping) {
                    return isset($allowedUlMapping[$value]) ? $allowedUlMapping[$value] : $value;
                }, $oldAllowedUlArray);

                $oldAllowedUlTextString = implode(',', $oldAllowedUlText);
                Log::channel('employee_info')->info("Old UnPaid Leaves (UL): {$oldAllowedUlTextString}");
            } else {
                // Log None if no value existed before update
                Log::channel('employee_info')->info("Old UnPaid Leaves (UL): None");
            }

            $updatePersonal->allowed_ul = null;

            Log::channel('employee_info')->info("Updated UnPaid Leaves (UL): None");
        }

        // Prepare allowed_ul as an array (this will handle both null and comma-separated cases)
        $allowedUlArray = $updatePersonal->allowed_ul ? explode(',', $updatePersonal->allowed_ul) : [];
        $updatePersonal->save();

        // Log the completion of the profile update
        Log::channel('employee_info')->info("Personal Information Updated Successfully.\n");

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
                'allowed_ul' => $updatePersonal->allowed_ul, // The comma-separated string
                'allowed_ul_array' => $allowedUlArray // Pass the array of selected leave types
            ]
        ]);
    }





    public function updateVisaInfo(Request $request)
    {
        $authId = auth()->user();

        $serverTime = now();
        // Get the user profile by userProfileId
        $userProfile = User::find($request->input('userProfileId'));

        // Log the incoming request data (for debugging or tracking purposes)
        Log::channel('employee_info')->info("Visa Info Updated By: User ID: {$authId->id} , Username:{$authId->username} , Employee ID: {$authId->employee_id}");

        // Log the user profile being updated (including username and employee_id)
        Log::channel('employee_info')->info(
            'Updating Visa Info For: ' .
            'User Profile ID: ' . $userProfile->id .
            ' , Username: ' . $userProfile->username .
            ' , Employee ID: ' . $userProfile->employee_id . "\n"
        );

        // Find the visa information
        $visaInfo = VisaInfo::find($request->input('userProfileId'));

        if (!$visaInfo) {
            return response()->json(['message' => 'Visa information not found.'], 404);
        }

        // Log the current visa information before update
        Log::channel('employee_info')->info(
            'Old Visa Info: ' . PHP_EOL .
            '     Passport No: ' . $visaInfo->passport_no . PHP_EOL .
            '     Passport Issue Date: ' . ($visaInfo->p_issue_date ? Carbon::parse($visaInfo->p_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '     Passport Expiry Date: ' . ($visaInfo->p_expiry_date ? Carbon::parse($visaInfo->p_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            '     Visa No: ' . $visaInfo->visa_no . PHP_EOL .
            '     Visa Issue Date: ' . ($visaInfo->v_issue_date ? Carbon::parse($visaInfo->v_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '     Visa Expiry Date: ' . ($visaInfo->v_expiry_date ? Carbon::parse($visaInfo->v_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            '     Foreign No: ' . ($visaInfo->foreign_no ? $visaInfo->foreign_no : '') . PHP_EOL .
            '     Foreign Expiry Date: ' . ($visaInfo->f_expiry_date ? Carbon::parse($visaInfo->f_expiry_date)->format('d-M-Y') : '') . "\n"

        );


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

        // Log the current visa information after the update
        Log::channel('employee_info')->info(
            'Updated Visa Info: ' . PHP_EOL .
            '    Passport No: ' . $visaInfo->passport_no . PHP_EOL .
            '    Passport Issue Date: ' . ($visaInfo->p_issue_date ? Carbon::parse($visaInfo->p_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '    Passport Expiry Date: ' . ($visaInfo->p_expiry_date ? Carbon::parse($visaInfo->p_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            '    Visa No: ' . $visaInfo->visa_no . PHP_EOL .
            '    Visa Issue Date: ' . ($visaInfo->v_issue_date ? Carbon::parse($visaInfo->v_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '    Visa Expiry Date: ' . ($visaInfo->v_expiry_date ? Carbon::parse($visaInfo->v_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            '    Foreign No: ' . ($visaInfo->foreign_no ? $visaInfo->foreign_no : '') . PHP_EOL .
            '    Foreign Expiry Date: ' . ($visaInfo->f_expiry_date ? Carbon::parse($visaInfo->f_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            "Timestamp: {$serverTime}\n"
        );


        // Return the updated information as JSON
        return response()->json([
            'visaInfo' => $visaInfo,
        ]);
    }


    //Emergency Update

    public function updateEmergency(Request $request)
    {

        $authId = auth()->user();
        $serverTime = now();
        $emergencyProfile = User::find($request->input('emergencyProfileId'));

        // Log the incoming request data (for debugging or tracking purposes)
        Log::channel('employee_info')->info("Emergency Info Updated By: User ID: {$authId->id} , Username:{$authId->username} , Employee ID: {$authId->employee_id}");

        // Log the user profile being updated (including username and employee_id)
        Log::channel('employee_info')->info(
            'Updating Emergency Info For: ' .
            'User Profile ID: ' . $emergencyProfile->id .
            ' , Username: ' . $emergencyProfile->username .
            ' , Employee ID: ' . $emergencyProfile->employee_id . "\n"
        );


        // Find the emergency contact associated with the user ID
        $emergency = Emergency::where('user_id', $request->emergencyProfileId)->first();

        // If the record exists, update it; otherwise, create a new one
        if ($emergency) {

            // Log the old emergency contact details before the update
            Log::channel('employee_info')->info(
                'Old Emergency Contact Info: ' . PHP_EOL .
                '    Name: ' . $emergency->e_name . PHP_EOL .
                '    Phone: ' . $emergency->e_phone . PHP_EOL .
                '    Email: ' . $emergency->e_email . PHP_EOL .
                '    Address: ' . $emergency->e_address . PHP_EOL .
                '    Country: ' . $emergency->e_country . PHP_EOL .
                '    Gender: ' . $emergency->e_gender . PHP_EOL .
                '    Relationship: ' . $emergency->e_relationship . PHP_EOL
            );

            $emergency->e_name = $request->emergency_name;
            $emergency->e_phone = $request->emergency_phone;
            $emergency->e_email = $request->emergency_email;
            $emergency->e_address = $request->emergency_address;
            $emergency->e_country = $request->emergency_country;
            $emergency->e_gender = $request->emergency_gender;
            $emergency->e_relationship = $request->emergency_relation;
            $emergency->save();


            // Log the new emergency contact details after the update
            Log::channel('employee_info')->info(
                'Updated Emergency Contact Info: ' . PHP_EOL .
                '    Name: ' . $emergency->e_name . PHP_EOL .
                '    Phone: ' . $emergency->e_phone . PHP_EOL .
                '    Email: ' . $emergency->e_email . PHP_EOL .
                '    Address: ' . $emergency->e_address . PHP_EOL .
                '    Country: ' . $emergency->e_country . PHP_EOL .
                '    Gender: ' . $emergency->e_gender . PHP_EOL .
                '    Relationship: ' . $emergency->e_relationship . PHP_EOL .
                "Timestamp: {$serverTime}\n"
            );

            return response()->json([
                'status' => 'success',
                'data' => $emergency,
            ]);
        }

        return redirect()->route('user-profile.index')->with('status', 'Emergency contact added successfully');
    }

    public function updatedependant(Request $request)
    {
        $authId = auth()->user();
        $serverTime = now();
        $dependantProfile = User::find($request->input('dependantProfileId'));

        // Log the incoming request data (for debugging or tracking purposes)
        Log::channel('employee_info')->info("Dependant Info Updated By: User ID: {$authId->id} , Username:{$authId->username} , Employee ID: {$authId->employee_id}");

        // Log the user profile being updated (including username and employee_id)
        Log::channel('employee_info')->info(
            'Updating Dependant Info For: ' .
            'User Profile ID: ' . $dependantProfile->id .
            ' , Username: ' . $dependantProfile->username .
            ' , Employee ID: ' . $dependantProfile->employee_id . "\n"
        );



        // Find the dependant by ID
        $dependant = dependant::findOrFail($request->dependantProfileId);


        // Log the old dependant data before updating
        Log::channel('employee_info')->info(
            'Old Dependant Info: ' . PHP_EOL .
            '      Name: ' . $dependant->d_name . PHP_EOL .
            '      Gender: ' . $dependant->d_gender . PHP_EOL .
            '      Nationality: ' . $dependant->d_nationality . PHP_EOL .
            '      Date of Birth: ' . ($dependant->d_dob ? Carbon::parse($dependant->d_dob)->format('d-M-Y') : '') . PHP_EOL .
            '      Passport No: ' . $dependant->d_passport_no . PHP_EOL .
            '      Passport Issue Date: ' . ($dependant->d_pass_issue_date ? Carbon::parse($dependant->d_pass_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '      Passport Expiry Date: ' . ($dependant->d_pass_expiry_date ? Carbon::parse($dependant->d_pass_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            '      Visa No: ' . $dependant->d_visa_no . PHP_EOL .
            '      Visa Issue Date: ' . ($dependant->d_visa_issue_date ? Carbon::parse($dependant->d_visa_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '      Visa Expiry Date: ' . ($dependant->d_visa_expiry_date ? Carbon::parse($dependant->d_visa_expiry_date)->format('d-M-Y') : '') . PHP_EOL
        );


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

        // Log the new dependant data after updating
        Log::channel('employee_info')->info(
            'Updated Dependant Info: ' . PHP_EOL .
            '      Name: ' . $dependant->d_name . PHP_EOL .
            '      Gender: ' . $dependant->d_gender . PHP_EOL .
            '      Nationality: ' . $dependant->d_nationality . PHP_EOL .
            '      Date of Birth: ' . ($dependant->d_dob ? Carbon::parse($dependant->d_dob)->format('d-M-Y') : '') . PHP_EOL .
            '      Passport No: ' . $dependant->d_passport_no . PHP_EOL .
            '      Passport Issue Date: ' . ($dependant->d_pass_issue_date ? Carbon::parse($dependant->d_pass_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '      Passport Expiry Date: ' . ($dependant->d_pass_expiry_date ? Carbon::parse($dependant->d_pass_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            '      Visa No: ' . $dependant->d_visa_no . PHP_EOL .
            '      Visa Issue Date: ' . ($dependant->d_visa_issue_date ? Carbon::parse($dependant->d_visa_issue_date)->format('d-M-Y') : '') . PHP_EOL .
            '      Visa Expiry Date: ' . ($dependant->d_visa_expiry_date ? Carbon::parse($dependant->d_visa_expiry_date)->format('d-M-Y') : '') . PHP_EOL .
            "Timestamp: {$serverTime}\n"
        );

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