<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Join the Department table to get department names
            $data = Designation::select('designations.id', 'designations.name as designation_name', 'departments.name as department_name')
                ->join('departments', 'designations.department_id', '=', 'departments.id'); // Join Designation with Department table

            return DataTables::of($data)->make(true); // Return DataTables response
        }

        // Pass departments data to the view for the form
        $departments = Department::all();
        return view('designation', compact('departments'));
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