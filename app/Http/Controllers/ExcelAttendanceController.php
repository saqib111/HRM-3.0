<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Excel;
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
            Excel::import(new AttendanceImport, $request->file('file'));
            // Return success response
            return response()->json(['message' => 'Schedule updated successfully!'], 200);
        } catch (\Exception $e) {
            // Handle errors for this specific module
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422); // 422 for validation-related errors
        }
    }
}
