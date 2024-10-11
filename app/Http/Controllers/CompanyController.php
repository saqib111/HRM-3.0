<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class CompanyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Company::select(['id', 'name']); // Select relevant fields
            return DataTables::of($data)->make(true); // Return DataTables response
        }
        return view('company'); // Return the main view
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

        // Create the company
        Company::create(['name' => $request->name]);

        return response()->json(['success' => 'Company added successfully.']);
    }

    public function show(Company $company)
    {
        return response()->json($company);
    }

    public function edit(Company $company)
    {
        return response()->json($company);
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company->update(['name' => $request->name]);

        return response()->json(['success' => 'Company updated successfully.']);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(['success' => 'Company deleted successfully.']);
    }
}