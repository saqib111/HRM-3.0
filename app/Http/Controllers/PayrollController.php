<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
class PayrollController extends Controller
{
    public function salary_deduction_index()
    {
        return view('payroll.salary_deduction');
    }

    public function salary_deduction_dynamic_data(Request $request)
    {
        $startDateInput = $request->input('start_date'); // Expecting "YYYY-MM-DD,YYYY-MM-DD"
        if ($startDateInput) {
            [$startDate, $endDate] = explode(',', $startDateInput); // Split the start and end dates
            $endDate = date('Y-m-d 23:59:59', strtotime($endDate)); // Adjust the end date
        } else {
            $startDate = null;
            $endDate = null;
        }

        $selectedNationality = $request->input('nationality', 'ALL'); // Default to "ALL"
        $selectedOffice = $request->input('office', 'AllOffices'); // Default to "AllOffices"

        $excludedIds = [121, 200, 300]; // Array of IDs to exclude

        $users = User::select(
            'users.id as user_id',
            'users.username',
            'users.employee_id',
            DB::raw('COUNT(CASE WHEN attendance_records.dayoff = "Yes" THEN 1 ELSE NULL END) as dayoff_count'),
            DB::raw('COUNT(CASE 
                    WHEN attendance_records.shift_in IS NOT NULL AND attendance_records.shift_out IS NOT NULL 
                         AND attendance_records.check_in IS NULL AND attendance_records.check_out IS NULL 
                         AND attendance_records.dayoff = "No" 
                         AND NOT EXISTS (
                             SELECT 1 
                             FROM approved_leaves 
                             WHERE approved_leaves.user_id = attendance_records.user_id 
                               AND approved_leaves.date = DATE(attendance_records.shift_in)
                         ) THEN 1 
                    ELSE NULL 
                END) as total_absentees'),
            DB::raw('SUM(CASE 
                    WHEN attendance_records.shift_in IS NOT NULL AND attendance_records.shift_out IS NOT NULL 
                         AND attendance_records.check_in IS NULL AND attendance_records.check_out IS NULL 
                         AND attendance_records.dayoff = "No" 
                         AND NOT EXISTS (
                             SELECT 1 
                             FROM approved_leaves 
                             WHERE approved_leaves.user_id = attendance_records.user_id 
                               AND approved_leaves.date = DATE(attendance_records.shift_in)
                         ) THEN 4 
                    ELSE 0 
                END) as absentee_fine'),
            DB::raw('SUM(CASE 
                    WHEN attendance_records.dayoff = "Yes" THEN 0
                    WHEN attendance_records.check_in > attendance_records.shift_in THEN 
                        CEIL(TIMESTAMPDIFF(MINUTE, attendance_records.shift_in, attendance_records.check_in) / 15) * 0.125
                    ELSE 0 
                END +
                CASE 
                    WHEN attendance_records.dayoff = "Yes" THEN 0
                    WHEN attendance_records.check_out < attendance_records.shift_out THEN 
                        CEIL(TIMESTAMPDIFF(MINUTE, attendance_records.check_out, attendance_records.shift_out) / 15) * 0.125
                    ELSE 0 
                END) as late_fine'),
            DB::raw('(SELECT COUNT(*) 
              FROM approved_leaves 
              WHERE approved_leaves.user_id = users.id 
                AND approved_leaves.date BETWEEN "' . $startDate . '" AND "' . $endDate . '") as leave_count')
        )
            ->leftJoin('attendance_records', function ($join) use ($startDate, $endDate) {
                $join->on('users.id', '=', 'attendance_records.user_id')
                    ->whereBetween('attendance_records.shift_in', [$startDate, $endDate]);
            })
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id') // Join with user_profile for nationality
            ->when($selectedNationality !== 'ALL', function ($query) use ($selectedNationality) {
                return $query->where('user_profiles.nationality', $selectedNationality); // Filter by nationality if not "ALL"
            })
            ->when($selectedOffice !== 'AllOffices', function ($query) use ($selectedOffice) {
                return $query->where('user_profiles.office', $selectedOffice); // Filter by office if not "AllOffices"
            })
            ->where('users.status', "1") // Include only activated users
            ->whereNotIn('users.id', $excludedIds) // Exclude specific user IDs
            ->groupBy('users.id', 'users.username', 'users.employee_id')
            ->orderBy('users.id', 'asc')
            ->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('dayoff', function ($user) {
                return $user->dayoff_count ?? 0; // Default to 0 if no dayoff records found
            })
            ->addColumn('leave_count', function ($user) {
                return $user->leave_count ?? 0; // Number of approved leaves
            })
            ->addColumn('total_absentees', function ($user) {
                return $user->total_absentees ?? 0; // Total absent days
            })
            ->addColumn('absentee_fine', function ($user) {
                return round($user->absentee_fine ?? 0, 3) . '%'; // Absentee fine
            })
            ->addColumn('late_fine', function ($user) {
                return round($user->late_fine ?? 0, 3) . '%'; // Late fine
            })
            ->addColumn('total_deduction', function ($user) {
                return round($user->absentee_fine + $user->late_fine, 3) . '%'; // Explicit sum of fines
            })
            ->make(true);
    }
}
