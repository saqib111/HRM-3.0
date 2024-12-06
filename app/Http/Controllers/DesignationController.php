<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Fetch user permissions
        $permissions = getUserPermissions($user);
        $canUpdateDesignation = $user->role == 1 || in_array('update_designation', $permissions);
        $canDeleteDesignation = $user->role == 1 || in_array('delete_designation', $permissions);

        if ($request->ajax()) {
            // Get data from the database
            $collection = DB::table('designations as de')
                ->join('departments as dt', 'dt.id', '=', 'de.department_id')
                ->select(
                    'de.id as id',  // Add the id here
                    'de.name as name',
                    'dt.name as department_name',
                ) // Select id and name fields
                ->orderBy('de.id', 'asc');

            // Return DataTables response with the correct columns
            return DataTables::of($collection)
                ->addIndexColumn() // Add index column
                ->addColumn('action', function ($row) use ($canUpdateDesignation, $canDeleteDesignation) {
                    return [
                        'edit' => $canUpdateDesignation,
                        'delete' => $canDeleteDesignation,
                    ];
                })
                ->make(true);
        }
        $departments = Department::all();

        return view('designation', compact('departments')); // Return the main view
    }

    public function create()
    {
        // Not used for this setup
    }

    public function store(Request $request)
    {
        // Validate the incoming request with additional validation for unique name and department_id combination
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer',
        ]);

        // Check if the combination of the same designation name and department already exists
        $exists = Designation::where('name', $request->name)
            ->where('department_id', $request->department_id)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'This designation already exists in the selected department. Please choose a different name or department.']);
        }

        // Create the Designation if it passes validation
        Designation::create([
            'name' => $request->name,
            'department_id' => $request->department_id,
        ]);

        return response()->json(['success' => 'Designation added successfully.']);
    }



    public function show(Designation $designation)
    {
        return response()->json($designation);
    }

    public function edit(Designation $designation)
    {

        return response()->json($designation);
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|exists:departments,id', // Ensure department ID exists
        ]);

        $designation->update(['name' => $request->name, 'department_id' => $request->department,]);

        return response()->json(['success' => 'Department updated successfully.']);
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return response()->json(['success' => 'Department deleted successfully.']);
    }
}