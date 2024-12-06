<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Fetch user permissions
        $permissions = getUserPermissions($user);
        $canUpdatebrand = $user->role == 1 || in_array('update_brand', $permissions);
        $canDeletebrand = $user->role == 1 || in_array('delete_brand', $permissions);

        if ($request->ajax()) {
            // Get data from the database
            $collection = DB::table('brands')
                ->select('id', 'name') // Select id and name fields
                ->orderBy('id', 'asc');

            // Return DataTables response with the correct columns
            return DataTables::of($collection)
                ->addIndexColumn() // Add index column
                ->addColumn('action', function ($row) use ($canUpdatebrand, $canDeletebrand) {
                    return [
                        'edit' => $canUpdatebrand,
                        'delete' => $canDeletebrand,
                    ];
                })
                ->make(true);
        }

        return view('brand'); // Return the main view
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

        // Check if the brand name already exists
        $exists = Brand::where('name', $request->name)->exists();
        if ($exists) {
            return response()->json(['error' => 'This brand name already exists. Please choose another name.']);
        }

        // Create the brand
        Brand::create(['name' => $request->name]);

        return response()->json(['success' => 'Brand added successfully.']);
    }

    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    public function edit(Brand $brand)
    {
        return response()->json($brand);
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $brand->update(['name' => $request->name]);

        return response()->json(['success' => 'Brand updated successfully.']);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json(['success' => 'Brand deleted successfully.']);
    }
}