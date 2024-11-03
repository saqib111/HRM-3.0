<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\{

    User,
    UserProfile,
    Company,
    Department,
    Designation,
    Brand,
    VisaInfo,
    AnnualLeaves,

};
use Illuminate\Support\Facades\{
    DB,
    Session,
    Http
};


class AdminController extends Controller
{
    public function dashboard()
    {

        if (auth()->user()) {
            return view('dashboard');
        }

    }
    public function addEmployee(Request $request)
    {
        // Define validation rules
        $rules = [
            'email' => 'required|email|max:50',
            'employee_id' => 'required|unique:users,employee_id',
            'username' => 'required|max:50',
            'company' => 'required',
            'department' => 'required',
            'designation' => 'required',
            'brand' => 'required|array|min:1',
            'joining_date' => 'required|date',
            'leave_type' => 'required', // Ensure leave type is provided and valid
        ];

        // Add image validation only if the file is present
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,webp|max:2048';
        }

        // Validate request
        $request->validate($rules);

        $img = 'images/default_profile_picture.png'; // Default image path

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $img = uploadImage($image); // Attempt to upload the image
        }
        // Process the selected brands array into a comma-separated string
        $brands = implode(',', $request->input('brand')); // Convert array to comma-separated string

        $user = new User();
        $user->employee_id = $request->employee_id;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->joining_date = date('Y-m-d', strtotime($request->joining_date));
        $user->company_id = $request->company;
        $user->department_id = $request->department;
        $user->designation_id = $request->designation;
        $user->brand = $brands; // Save the comma-separated brands
        $user->image = $img; // Store the image path
        $user->save();
        $user_id = $user->id;

        // Add profile and visa information
        $profile = new UserProfile();
        $profile->user_id = $user_id;
        $profile->save();

        $visa = new VisaInfo();
        $visa->user_id = $user_id;
        $visa->save();

        // Calculate Annual Leaves
        $leave_type_days = (int) $request->leave_type; // Fetch the leave type (14 or 28)
        $joining_date = date_create($request->joining_date);
        $end_of_year = date_create(date("Y") . "-12-31");
        $remaining_days = date_diff($joining_date, $end_of_year)->days;
        $total_days_in_year = 365;

        // Calculate the annual leave balance
        $calculated_leaves = ($remaining_days / $total_days_in_year) * $leave_type_days;

        // Apply rounding logic
        $decimal_part = $calculated_leaves - floor($calculated_leaves);
        // Round the decimal part to two decimal places
        $decimal_part = round($decimal_part, 4);

        if ($decimal_part >= 0.76) {
            $calculated_leaves = ceil($calculated_leaves);
        } elseif ($decimal_part >= 0.26 && $decimal_part <= 0.75) {
            $calculated_leaves = floor($calculated_leaves) + 0.5;
        } else {
            $calculated_leaves = floor($calculated_leaves);
        }

        // Create a new AnnualLeaves instance and save it
        $annualLeave = new AnnualLeaves();
        $annualLeave->user_id = $user_id; // Link the annual leave information to the user
        $annualLeave->leave_type = $leave_type_days; // Total annual leave based on employee type (14 or 28)
        $annualLeave->leave_balance = $calculated_leaves; // Set calculated leave balance
        $annualLeave->last_year_balance = 0; // Set default value for last year's balance
        $annualLeave->save(); // Save the annual leave information

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully!',
        ], 200);
    }

    public function getEmployee(Request $request)
    {
        $brand = Brand::all();
        $company = Company::all();
        $department = Department::all();

        if ($request->ajax()) {

            // Join users with companies table
            $employees = User::join('companies', 'users.company_id', '=', 'companies.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->join('designations', 'users.designation_id', '=', 'designations.id')
                ->select([
                    'users.id',
                    'users.employee_id',
                    'users.username',
                    'users.email',
                    'users.joining_date',
                    'companies.name as company_name',  // Selecting company name
                    'departments.name as department_name',  // Department Name
                    'designations.name as designation_name',  // Designation Name
                    'users.status',
                    'users.image'
                ]);

            // Filter by company if specified
            if ($request->has('company')) {
                $employees->where('companies.name', $request->input('company'));
            }

            return DataTables::of($employees)
                ->addIndexColumn()
                // Make company_name searchable using the "whereHas" filter for joined fields
                ->filterColumn('company_name', function ($query, $keyword) {
                    $query->where('companies.name', 'LIKE', "%{$keyword}%");
                })
                // Make department_name searchable
                ->filterColumn('department_name', function ($query, $keyword) {
                    $query->where('departments.name', 'LIKE', "%{$keyword}%");
                })
                // Make designation_name searchable
                ->filterColumn('designation_name', function ($query, $keyword) {
                    $query->where('designations.name', 'LIKE', "%{$keyword}%");
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-danger" onclick="deleteUser(' . $row->id . ')"><i class="fa fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('employees', ['company' => $company, 'brand' => $brand, 'department' => $department]);
    }


    public function deleteEmployee($id)
    {
        $user = User::find($id);

        if ($user) {
            // Check if the image is not the default image before deleting
            if (
                $user->image !== 'images/default_profile_picture.png' &&
                Storage::disk('public')->exists($user->image)
            ) {
                Storage::disk('public')->delete($user->image);
            }

            // Now delete the user
            $user->delete();
            return response()->json(['success' => 'Employee deleted successfully.']);
        }

        return response()->json(['error' => 'Employee not found.'], 404);
    }

    public function editEmployee($id)
    {
        $employee = User::find($id);
        $brands = explode(',', $employee->brand);
        $img = getImageUrl($employee->image);

        return response()->json([$employee, $brands, $img]);
    }

    public function updateEmployee(Request $request)
    {
        // Find the employee by ID
        $employee = User::find($request->id);

        // Initialize the image variable
        $image = $employee->image;

        // Check if a new image is being uploaded
        if ($request->hasFile('image')) {
            // Check if the old image exists and is not the default image
            if (
                $employee->image !== 'images/default_profile_picture.png' &&
                Storage::disk('public')->exists($employee->image)
            ) {
                Storage::disk('public')->delete($employee->image);
            }

            // Upload the new image and get the new image path
            $image = uploadImage($request->image);
        }

        // Update employee details
        $employee->employee_id = $request->employee_id; // Set the fields directly
        $employee->username = $request->username;
        $employee->email = $request->email;
        $employee->joining_date = date('Y-m-d', strtotime($request->joining_date));
        $employee->company_id = $request->company;
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->brand = implode(',', $request->brand);
        $employee->image = $image; // Set the image path to the new or existing one

        // Save the employee record
        $employee->save(); // Use save() to persist changes

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully!',
        ], 200);
    }




    public function updateStatus(Request $request)
    {
        // Validate the parameters
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string',
        ]);

        $id = $request->input('id');
        $status = $request->input('status');

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update the user's status
        $user->status = $status;
        $user->save(); // Persist changes

        // Return a successful response
        return response()->json([
            'id' => $id,
            'status' => $status,
            'message' => 'User status updated successfully!'
        ], 200);
    }

    public function checkDesignation($id)
    {
        $designation = DB::table('designations')
            ->select('*')
            ->where('department_id', '=', $id)
            ->get();

        return response()->json($designation);
    }

    public function updatePassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id', // Ensure user exists in the users table
            'password' => 'required|string|min:8|confirmed', // Validate password and confirmation
        ]);

        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Fetch the user using the provided employee_id
        $user = User::findOrFail($request->employee_id);

        // Update the user's password
        $user->password = Hash::make($request->password); // Hash the new password
        $user->save();

        // Return a success response
        return response()->json(['message' => 'Password updated successfully.'], 200);
    }

    public function getEmployeeId($id)
    {
        $employee = User::find($id); // Assuming you're fetching from the User model
        if ($employee) {
            return response()->json(['employee_id' => $employee->id]); // Ensure you return employee_id
        }
        return response()->json(['message' => 'Employee not found'], 404);
    }
}