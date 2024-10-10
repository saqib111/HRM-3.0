<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\{

    User,
    UserProfile,
    Company,
    Department,
    Designation,
    Brand,
    VisaInfo,

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
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Image validation with max size 2MB
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email|max:50',
            'employee_id' => 'required|unique:users,employee_id', // Ensure employee_id is unique
            'username' => 'required|max:50',
            'company' => 'required',
            'department' => 'required',
            'designation' => 'required',
            'brand' => 'required|array|min:1', // Validate that at least one brand is selected
            'joining_date' => 'required|date',
        ]);

        // Handle the image upload and optimization
        $image = $request->file('image');
        $img = uploadImage($image);

        if ($img != null) {
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

            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully!',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image upload failed.',
        ], 400);
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
                    'users.status'
                ]);

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
        if ($request->image != null) {
            if (Storage::disk('public')->exists($request->old_image)) {
                Storage::disk('public')->delete($request->old_image);
            }
            $img = uploadImage($request->image);
            $image = $img;

        } else {

            if ($request->old_image != null) {
                $image = $request->old_image;
            } else {
                $image = null;
            }
        }

        $employee = User::find($request->id);
        $employee->update([
            $employee->employee_id = $request->employee_id,
            $employee->username = $request->username,
            $employee->email = $request->email,
            $employee->joining_date = date('Y-m-d', strtotime($request->joining_date)),
            $employee->company_id = $request->company,
            $employee->department_id = $request->department,
            $employee->designation_id = $request->designation,
            $employee->brand = implode(',', $request->brand),
            $employee->image = $image,

        ]);




        return response()->json([
            'success' => true,
            'message' => 'Employee Updated successfully!',
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
}