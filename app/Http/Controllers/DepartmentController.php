<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Fetch user permissions
        $permissions = getUserPermissions($user);
        $canUpdateDepartment = $user->role == 1 || in_array('update_department', $permissions);
        $canDeleteDepartment = $user->role == 1 || in_array('delete_department', $permissions);

        if ($request->ajax()) {
            // Get data from the database
            $collection = DB::table('departments')
                ->select('id', 'name') // Select id and name fields
                ->orderBy('id', 'asc');

            // Return DataTables response with the correct columns
            return DataTables::of($collection)
                ->addIndexColumn() // Add index column
                ->addColumn('action', function ($row) use ($canUpdateDepartment, $canDeleteDepartment) {
                    return [
                        'edit' => $canUpdateDepartment,
                        'delete' => $canDeleteDepartment,
                    ];
                })
                ->make(true);
        }

        return view('department'); // Return the main view
    }

    public function create()
    {
        // Not used for this setup
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check if the Department name already exists
        $exists = Department::where('name', $request->name)->exists();
        if ($exists) {
            return response()->json(['error' => 'This department name already exists. Please choose another name.']);
        }

        // Create the Department
        Department::create(['name' => $request->name]);

        return response()->json(['success' => 'Department added successfully.']);
    }

    public function show(Department $department)
    {
        return response()->json($department);
    }

    public function edit(Department $department)
    {
        return response()->json($department);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->update(['name' => $request->name]);

        return response()->json(['success' => 'Department updated successfully.']);
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return response()->json(['success' => 'Department deleted successfully.']);
    }
}