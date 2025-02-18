<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AttendanceImport;

class ExcelAttendanceController extends Controller
{
    public function importdata(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            // Import and capture the instance of AttendanceImport
            $import = new AttendanceImport;
            Excel::import($import, $request->file('file'));

            // Now you can access the totalRecords property
            $totalRecords = $import->totalRecords;

            // Return success response with the total number of records
            return response()->json([
                'success' => true,
                'message' => "{$totalRecords} Records added successfully!",
            ], 200);
        } catch (\Exception $e) {
            // Handle errors for this specific module
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422); // 422 for validation-related errors
        }
    }
}
