<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
class PayrollController extends Controller
{
    public function salary_deduction_index()
    {
        return view('payroll.salary_deduction');
    }

    public function salary_deduction_dynamic_data(Request $request)
    {
        $user_id = auth()->user()->id;
        $user_role = auth()->user()->role;
        $team_data = DB::table("leader_employees")->where("Leader_id", $user_id)->pluck("employee_id")->toArray();
        $team_data[] = $user_id;

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

        $excludedIds = [13, 14, 17, 19, 24, 31, 32, 83, 110, 118, 191, 193, 197, 209, 227, 230, 246, 308, 315, 378, 400, 410, 451, 523, 524, 606, 738, 1103, 1590]; // Array of IDs to exclude

        $users = User::select(
            'users.id as user_id',
            'users.username',
            'users.employee_id',
            'users.week_days', // Fetch the week_days field
            DB::raw('COUNT(CASE WHEN attendance_records.dayoff = "Yes" OR attendance_records.dayoff = "PH" OR attendance_records.dayoff = "BT" THEN 1 ELSE NULL END) as dayoff_count'),
            DB::raw('COUNT(CASE 
                    WHEN attendance_records.shift_in IS NOT NULL 
                        AND attendance_records.shift_out IS NOT NULL 
                        AND attendance_records.check_in IS NULL 
                        AND attendance_records.check_out IS NULL 
                        AND attendance_records.dayoff = "No" 
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM approved_leaves 
                            WHERE approved_leaves.user_id = attendance_records.user_id 
                            AND approved_leaves.date = DATE(attendance_records.shift_in)
                            AND approved_leaves.leave_type IN (1, 2, 3, 4, 5, 6, 7, 8)
                        ) THEN 1 
                    ELSE NULL 
                END) as total_absentees'),
            DB::raw('SUM(CASE 
                    WHEN attendance_records.shift_in IS NOT NULL 
                        AND attendance_records.shift_out IS NOT NULL 
                        AND attendance_records.check_in IS NULL 
                        AND attendance_records.check_out IS NULL 
                        AND attendance_records.dayoff = "No" 
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM approved_leaves 
                            WHERE approved_leaves.user_id = attendance_records.user_id 
                            AND approved_leaves.date = DATE(attendance_records.shift_in)
                            AND approved_leaves.leave_type IN (1, 2, 3, 4, 5, 6, 7, 8)
                        ) THEN 
                            CASE 
                                WHEN users.week_days = "5" THEN 4.8 
                                ELSE 4.0 
                            END 
                    ELSE 0 
                END) as absentee_fine'),
            DB::raw('SUM(
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM approved_leaves 
                    WHERE approved_leaves.user_id = attendance_records.user_id 
                      AND approved_leaves.date = DATE(attendance_records.shift_in)
                      AND approved_leaves.leave_type IN (1, 2, 3, 4, 5, 6, 7, 8)
                ) THEN 0 -- No fine if leave is approved
                WHEN attendance_records.dayoff = "Yes" THEN 0
                WHEN attendance_records.check_in IS NOT NULL AND attendance_records.check_out IS NULL THEN 
                    CASE 
                        WHEN users.week_days = "5" THEN 4.8 -- Deduction for 5-day workers
                        ELSE 4.0 -- Deduction for 6-day workers
                    END
                WHEN DATE(attendance_records.check_out) != DATE(attendance_records.shift_out) THEN 
                    CASE 
                        WHEN users.week_days = "5" THEN 4.8 -- Deduction for 5-day workers
                        ELSE 4.0 -- Deduction for 6-day workers
                    END
                WHEN attendance_records.check_in IS NOT NULL AND attendance_records.check_out IS NOT NULL THEN 
                    LEAST(
                        CASE 
                            WHEN users.week_days = "5" THEN 4.8 -- Cap for 5-day workers
                            ELSE 4.0 -- Cap for 6-day workers
                        END,
                        GREATEST(
                            CEIL(TIMESTAMPDIFF(MINUTE, attendance_records.shift_in, attendance_records.check_in) / 15) *
                                CASE 
                                    WHEN users.week_days = "5" THEN 0.25 -- Late fine per 15 mins for 5-day workers
                                    ELSE 0.125 -- Late fine per 15 mins for 6-day workers
                                END, 0
                        ) +
                        GREATEST(
                            CEIL(TIMESTAMPDIFF(MINUTE, attendance_records.check_out, attendance_records.shift_out) / 15) *
                                CASE 
                                    WHEN users.week_days = "5" THEN 0.25 -- Late fine per 15 mins for 5-day workers
                                    ELSE 0.125 -- Late fine per 15 mins for 6-day workers
                                END, 0
                        )
                    )
                ELSE 0 
            END
        ) as late_fine'),
            DB::raw('(SELECT COUNT(*) 
            FROM approved_leaves 
            WHERE approved_leaves.user_id = users.id 
            AND approved_leaves.date BETWEEN "' . $startDate . '" AND "' . $endDate . '"
            AND approved_leaves.leave_type != 4) as leave_count'),
            DB::raw('(SELECT COUNT(*) 
            FROM approved_leaves 
            WHERE approved_leaves.user_id = users.id 
            AND approved_leaves.date BETWEEN "' . $startDate . '" AND "' . $endDate . '"
            AND approved_leaves.leave_type = 4) as unpaid_leave_count') // New column for unpaid leave count
        )
            ->leftJoin('attendance_records', function ($join) use ($startDate, $endDate) {
                $join->on('users.id', '=', 'attendance_records.user_id')
                    ->whereBetween('attendance_records.shift_in', [$startDate, $endDate]);
            })
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id') // Join with user_profile for nationality
            ->when($user_role == "5" || $user_role == "2", function ($query) use ($user_id) {
                // If the user role is not 1, filter only by the authenticated user's ID
                return $query->where('users.id', $user_id);
            })
            ->when($user_role == "1" || $user_role == "3", function ($query) use ($selectedNationality, $selectedOffice) {
                // If the user role is 1, apply nationality and office filters
                return $query->when($selectedNationality !== 'ALL', function ($query) use ($selectedNationality) {
                    return $query->where('user_profiles.nationality', $selectedNationality);
                })->when($selectedOffice !== 'AllOffices', function ($query) use ($selectedOffice) {
                    return $query->where('user_profiles.office', $selectedOffice);
                });
            })
            ->when($user_role == "4", function ($query) use ($team_data) {
                return $query->whereIn("users.id", $team_data);
            })
            ->where('users.status', "1") // Include only activated users
            ->whereNotIn('users.id', $excludedIds) // Exclude specific user IDs
            ->groupBy('users.id', 'users.username', 'users.employee_id', 'users.week_days')
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
                return round($user->late_fine ?? 0, 3) . '%'; // Late fine capped per day
            })
            ->addColumn('total_deduction', function ($user) {
                return round($user->absentee_fine + $user->late_fine, 3) . '%'; // Total deduction
            })
            ->addColumn('unpaid_leave_count', function ($user) {
                return $user->unpaid_leave_count ?? 0; // Default to 0 if no unpaid leave records found
            })
            ->make(true);
    }




    // Export function
    public function export(Request $request)
    {
        $user_id = auth()->user()->id;
        $user_role = auth()->user()->role;
        $team_data = DB::table("leader_employees")->where("Leader_id", $user_id)->pluck("employee_id")->toArray();
        $team_data[] = $user_id;

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

        $excludedIds = [13, 14, 17, 19, 24, 31, 32, 83, 110, 118, 191, 193, 197, 209, 227, 230, 246, 308, 315, 378, 400, 410, 451, 523, 524, 606, 738, 1103, 1590]; // Array of IDs to exclude

        $users = User::select(
            'users.id as user_id',
            'users.username',
            'users.employee_id',
            'users.week_days', // Fetch the week_days field
            DB::raw('COUNT(CASE WHEN attendance_records.dayoff = "Yes" OR attendance_records.dayoff = "PH" OR attendance_records.dayoff = "BT" THEN 1 ELSE NULL END) as dayoff_count'),
            DB::raw('COUNT(CASE 
                    WHEN attendance_records.shift_in IS NOT NULL 
                         AND attendance_records.shift_out IS NOT NULL 
                         AND attendance_records.check_in IS NULL 
                         AND attendance_records.check_out IS NULL 
                         AND attendance_records.dayoff = "No" 
                         AND NOT EXISTS (
                             SELECT 1 
                             FROM approved_leaves 
                             WHERE approved_leaves.user_id = attendance_records.user_id 
                               AND approved_leaves.date = DATE(attendance_records.shift_in)
                               AND approved_leaves.leave_type IN (1, 2, 3, 4, 5, 6, 7, 8)
                         ) THEN 1 
                    ELSE NULL 
                END) as total_absentees'),
            DB::raw('SUM(CASE 
                    WHEN attendance_records.shift_in IS NOT NULL 
                         AND attendance_records.shift_out IS NOT NULL 
                         AND attendance_records.check_in IS NULL 
                         AND attendance_records.check_out IS NULL 
                         AND attendance_records.dayoff = "No" 
                         AND NOT EXISTS (
                             SELECT 1 
                             FROM approved_leaves 
                             WHERE approved_leaves.user_id = attendance_records.user_id 
                               AND approved_leaves.date = DATE(attendance_records.shift_in)
                               AND approved_leaves.leave_type IN (1, 2, 3, 4, 5, 6, 7, 8)
                         ) THEN 
                            CASE 
                                WHEN users.week_days = "5" THEN 4.8 
                                ELSE 4.0 
                            END 
                    ELSE 0 
                END) as absentee_fine'),
            DB::raw('SUM(
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 
                            FROM approved_leaves 
                            WHERE approved_leaves.user_id = attendance_records.user_id 
                              AND approved_leaves.date = DATE(attendance_records.shift_in)
                              AND approved_leaves.leave_type IN (1, 2, 3, 4, 5, 6, 7, 8)
                        ) THEN 0 -- No fine if leave is approved
                        WHEN attendance_records.dayoff = "Yes" THEN 0
                        WHEN attendance_records.check_in IS NOT NULL AND attendance_records.check_out IS NULL THEN 
                            CASE 
                                WHEN users.week_days = "5" THEN 4.8 -- Deduction for 5-day workers
                                ELSE 4.0 -- Deduction for 6-day workers
                            END
                        WHEN DATE(attendance_records.check_out) != DATE(attendance_records.shift_out) THEN 
                            CASE 
                                WHEN users.week_days = "5" THEN 4.8 -- Deduction for 5-day workers
                                ELSE 4.0 -- Deduction for 6-day workers
                            END
                        WHEN attendance_records.check_in IS NOT NULL AND attendance_records.check_out IS NOT NULL THEN 
                            LEAST(
                                CASE 
                                    WHEN users.week_days = "5" THEN 4.8 -- Cap for 5-day workers
                                    ELSE 4.0 -- Cap for 6-day workers
                                END,
                                GREATEST(
                                    CEIL(TIMESTAMPDIFF(MINUTE, attendance_records.shift_in, attendance_records.check_in) / 15) *
                                        CASE 
                                            WHEN users.week_days = "5" THEN 0.25 -- Late fine per 15 mins for 5-day workers
                                            ELSE 0.125 -- Late fine per 15 mins for 6-day workers
                                        END, 0
                                ) +
                                GREATEST(
                                    CEIL(TIMESTAMPDIFF(MINUTE, attendance_records.check_out, attendance_records.shift_out) / 15) *
                                        CASE 
                                            WHEN users.week_days = "5" THEN 0.25 -- Late fine per 15 mins for 5-day workers
                                            ELSE 0.125 -- Late fine per 15 mins for 6-day workers
                                        END, 0
                                )
                            )
                        ELSE 0 
                    END
                ) as late_fine'),
            DB::raw('(SELECT COUNT(*) 
            FROM approved_leaves 
            WHERE approved_leaves.user_id = users.id 
            AND approved_leaves.date BETWEEN "' . $startDate . '" AND "' . $endDate . '"
            AND approved_leaves.leave_type != 4) as leave_count'),
            DB::raw('(SELECT COUNT(*) 
            FROM approved_leaves 
            WHERE approved_leaves.user_id = users.id 
            AND approved_leaves.date BETWEEN "' . $startDate . '" AND "' . $endDate . '"
            AND approved_leaves.leave_type = 4) as unpaid_leave_count') // New column for unpaid leave count
        )
            ->leftJoin('attendance_records', function ($join) use ($startDate, $endDate) {
                $join->on('users.id', '=', 'attendance_records.user_id')
                    ->whereBetween('attendance_records.shift_in', [$startDate, $endDate]);
            })
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->when($user_role == "5" || $user_role == "2", function ($query) use ($user_id) {
                // If the user role is not 1, filter only by the authenticated user's ID
                return $query->where('users.id', $user_id);
            })
            ->when($user_role == "1" || $user_role == "3", function ($query) use ($selectedNationality, $selectedOffice) {
                // If the user role is 1, apply nationality and office filters
                return $query->when($selectedNationality !== 'ALL', function ($query) use ($selectedNationality) {
                    return $query->where('user_profiles.nationality', $selectedNationality);
                })->when($selectedOffice !== 'AllOffices', function ($query) use ($selectedOffice) {
                    return $query->where('user_profiles.office', $selectedOffice);
                });
            })
            ->when($user_role == "4", function ($query) use ($team_data) {
                return $query->whereIn("users.id", $team_data);
            })
            ->where('users.status', "1")
            ->whereNotIn('users.id', $excludedIds)
            ->groupBy('users.id', 'users.username', 'users.employee_id', 'users.week_days')
            ->orderBy('users.id', 'asc')
            ->get();

        // Export the data using Laravel Excel
        return Excel::download(new PayrollExport($users), 'payroll_data.xlsx');
    }
}
