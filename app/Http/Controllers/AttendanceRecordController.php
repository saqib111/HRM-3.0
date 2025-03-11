<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\{
    DB,
    Session,
    Http
};
use App\Models\{

    User,
    LeaderEmployee,
    Group,
    AttendanceSession,
    Fingerprint,
    ApprovedLeave
};
use Illuminate\Support\Facades\Log;
use App\Logging\UpdateScheduleLog;
class AttendanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceRecord $attendanceRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceRecord $attendanceRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceRecord $attendanceRecord)
    {
        //
    }

    public function reloadTimesheet()
    {
        $serverTime = Carbon::now();
        $active_user_id = auth()->user()->id;

        // Fetch the latest shift record for the user
        $shift_record = AttendanceRecord::where('user_id', '=', $active_user_id)
            ->where(function ($query) {
                $query->whereDate('shift_in', Carbon::today())
                    ->orWhereDate('shift_out', Carbon::today());
            })
            ->whereNotNull('check_in')  // Ensure check_in is not null
            ->where('check_in', '!=', '')  // Ensure check_in is not empty
            ->whereNull('check_out')  // Ensure check_out is null (not punched out yet)
            ->select('shift_out', 'check_in')  // Select only the shift_out field
            ->first();
        return view('partials.timesheet', compact('serverTime', 'shift_record'))->render();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceRecord $attendanceRecord)
    {
        //
    }

    public function attendanceRecord()
    {
        $color = "";
        $info = [];
        $i = 0;
        $c_in = "";
        $c_out = "";
        $absent = "";
        $difference = 0;
        $v1 = "";
        $v2 = "";
        $id = auth()->user()->id;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $approvedLeaves = ApprovedLeave::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('user_id', $id)
            ->get();
        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'ar.user_id as user_id',
                'u.employee_id as emp_id',
                'ar.shift_in as shift_in',
                'ar.shift_out as shift_out',
                'ar.duty_hours as duty_hours',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff'
            )
            ->where('ar.user_id', $id)
            ->whereMonth('ar.shift_in', Carbon::now()->month)
            ->orderBy('ar.shift_in', 'asc') // Sort by `shift_in` on the server side
            ->get();

        foreach ($data as $d) {
            $start = Carbon::parse($d->shift_in);

            if ($approvedLeaves) {
                foreach ($approvedLeaves as $app) {
                    $date = Carbon::parse($app->date);
                    if ($app->start_time != null && $app->leave_type == "1" && $start->isSameDay($date)) {
                        $chdate = date('Y-m-d', strtotime($app->date));
                        $shift_in = $chdate . $app->start_time;
                        $shift_out = $chdate . $app->end_time;
                        $in = AttendanceRecord::whereDate('shift_in', $chdate)->where('user_id', $id)->first();

                        $up = AttendanceRecord::find($in->id);
                        // $up->update([
                        //     $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_in)),
                        //     $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out))
                        // ]);
                        $color = "";
                        break;
                    } else {
                        $color = "";
                    }


                    if ($start->isSameDay($date)) {
                        $color = getColorByLeaveType($app->leave_type);
                        break;
                    } else {
                        $color = "";
                    }
                }
            }
            if ($color == "" && $d->dayoff != "Yes") {
                $currentDate = Carbon::now();
                if ($start->lte($currentDate) && !$d->check_in && !$d->check_out) {
                    $absent = "Yes";
                } else {
                    $absent = "No";
                }
            }



            $i++;
            $closPunchIn = null;
            $smallDifferenceIn = PHP_INT_MAX;
            $closPunchOut = null;
            $smallDifferenceOut = PHP_INT_MAX;
            $emp_id = $d->emp_id;
            preg_match('/[1-9][0-9]*/', $emp_id, $actual_id);
            $employee_id = (int) $actual_id[0];
            $start_date = date('d F Y', strtotime($d->shift_in));
            $end_date = date('d F Y', strtotime($d->shift_out));
            if ($d->check_in != null) {
                $in_date = date('d F Y', strtotime($d->check_in));
            } else {
                $in_date = "";
            }
            if ($d->check_out != null) {
                $out_date = date('d F Y', strtotime($d->check_out));
            } else {
                $out_date = "";
            }

            $checkInTime = date('Y-m-d ', strtotime($d->shift_in));
            $check1 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkInTime)->where('type', 0)->get();


            if (count($check1) > 0) {
                foreach ($check1 as $c1) {
                    $difference = abs(strtotime($d->check_in) - strtotime($c1->fingerprint_in));
                    if ($difference < $smallDifferenceIn) {
                        $smallDifferenceIn = $difference;
                        $closestPunchIn = $c1;
                    }
                }

                if (!empty($closestPunchIn) && !empty($d->check_in)) {
                    $finger_print1 = date('Y-m-d H:i:s', strtotime($closestPunchIn->fingerprint_in));
                    $fp1 = Carbon::parse($finger_print1);
                    $checkIn1 = date('Y-m-d H:i:s', strtotime($d->check_in));
                    $ch1 = Carbon::parse($checkIn1);
                    $min_dif1 = $fp1->diffInMinutes($ch1);

                    if (abs($min_dif1) <= 10) {
                        $v1 = "yes";
                    } else {
                        $v1 = "cross";
                    }
                }
            } else {
                $v1 = "no";
            }
            $checkOutTime = date('Y-m-d ', strtotime($d->shift_out));
            $check2 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkOutTime)->where('type', '1')->get();
            if (count($check2) > 0) {
                foreach ($check2 as $c2) {
                    $difference2 = abs(strtotime($d->check_out) - strtotime($c2->fingerprint_in));
                    if ($difference2 < $smallDifferenceOut) {
                        $smallDifferenceOut = $difference2;
                        $closestPunchOut = $c2;
                    }
                }

                if (!empty($closestPunchOut) && !empty($d->check_out)) {
                    $finger_print2 = date('Y-m-d H:i:s', strtotime($closestPunchOut->fingerprint_in));
                    $fp2 = Carbon::parse($finger_print2);
                    $checkout = date('Y-m-d H:i:s', strtotime($d->check_out));
                    $co = Carbon::parse($checkout);
                    $min_dif2 = $fp2->diffInMinutes($co);
                    if (abs($min_dif2) <= 10) {
                        $v2 = "yes";
                    } else {
                        $v2 = "cross";
                    }
                }

            } else {
                $v2 = "no";
            }

            $name = $d->name;

            $shift_in = Carbon::parse($d->shift_in);
            $shift_out = Carbon::parse($d->shift_out);
            $scheduled_duration = $shift_in->diffInSeconds($shift_out);

            if ($d->check_in && $d->check_out) {
                $check_in = Carbon::parse($d->check_in);
                $check_out = Carbon::parse($d->check_out);

                // Calculate effective check-in and check-out within shift boundaries
                $effective_check_in = $check_in->lt($shift_in) ? $shift_in : $check_in;
                $effective_check_out = $check_out->gt($shift_out) ? $shift_out : $check_out;

                // Calculate the working duration in seconds
                $working_duration = $effective_check_in->diffInSeconds($effective_check_out);

                // Convert the working duration into hours, minutes, and seconds
                $hours = floor($working_duration / 3600);
                $minutes = floor(($working_duration % 3600) / 60);
                $seconds = $working_duration % 60;

                $duty_hours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            } else {
                // Default value if check-in or check-out is missing
                $duty_hours = "";
            }

            $info[] = [
                'no' => $i,
                'start_date' => $start_date,
                'name' => $d->name,
                'shift_in' => $shift_in->format('H:i:s'),
                'shift_out' => $shift_out->format('H:i:s'),
                'check_in' => $d->check_in ? Carbon::parse($d->check_in)->format('H:i:s') : null,
                'check_out' => $d->check_out ? Carbon::parse($d->check_out)->format('H:i:s') : null,
                'color' => $color,
                'duty_hours' => $duty_hours,
                'dayoff' => $d->dayoff,
                'end_date' => $end_date,
                'in_date' => $in_date,
                'out_date' => $out_date,
                'absent' => $absent,
                'verify1' => $v1,
                'verify2' => $v2,

            ];
        }
        return response()->json([

            'data' => $info,
        ], 200);

    }

    public function attendanceEmployeeRecord()
    {
        $serverTime = Carbon::now();
        $active_user_id = Auth()->user()->id;

        // Fetch the shift record for the user
        $shift_record = AttendanceRecord::where('user_id', '=', $active_user_id)
            ->where(function ($query) {
                $query->whereDate('shift_in', Carbon::today())
                    ->orWhereDate('shift_out', Carbon::today());
            })
            ->whereNotNull('check_in')  // Ensure check_in is not null
            ->where('check_in', '!=', '')  // Ensure check_in is not empty
            ->whereNull('check_out')  // Ensure check_out is null (not punched out yet)
            ->select('shift_out', 'check_in')  // Select only the shift_out field
            ->first();

        if (!$shift_record) {
            // Check for incomplete shift from the previous day
            $shift_record = AttendanceRecord::where('user_id', '=', $active_user_id)
                ->whereNotNull('check_in')  // Ensure check_in exists
                ->where('check_in', '!=', '')  // Ensure check_in is not empty
                ->whereNull('check_out')  // No checkout yet
                ->where('shift_out', '<', $serverTime)  // Ensure shift_out time is in the past
                ->whereDate('shift_in', '>=', Carbon::now()->subDays(7)->toDateString())
                ->select('shift_out')  // Only need shift_out to check if the user can punch out
                ->first();
        }

        // If still no record, return the view without shift record (meaning user is absent or no shift found)
        if (!$shift_record) {
            return view('attendance.attendance-employee', compact('serverTime', 'shift_record'));
        }

        return view('attendance.attendance-employee', compact('serverTime', 'shift_record'));
    }


    public function searchRecord(Request $request)
    {
        $id = auth()->user()->id;
        $color = "";
        $info = [];
        $i = 0;
        $c_in = "";
        $c_out = "";
        $absent = "";
        $difference = 0;
        $v1 = "";
        $v2 = "";
        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);


        if ($from->diffInMonths($to) > 3) {
            return response()->json(['error' => 'The date range cannot exceed 4 months.'], 400);
        }

        if ($from->gt($to)) {
            return response()->json(['error' => 'The "From Date" cannot be greater than the "To Date".'], 400);
        }

        $from = $from->format('Y-m-d H:i:s');
        $to = $to->format('Y-m-d H:i:s');

        $info = [];
        $i = 0;
        $total_late_minutes = 0;
        $stotal_late_minutes = 0;
        $total_penalty = 0.0;

        $fine = 0.0;
        $late_minutes = 0;
        $slate_minutes = 0;
        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'u.week_days as wd',
                'ar.user_id as user_id',
                'u.employee_id as emp_id',
                'ar.shift_in as shift_in',
                'ar.id as id',
                'ar.shift_out as shift_out',
                'ar.duty_hours as duty_hours',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff',
                'ar.id as id'

            )
            ->where('ar.user_id', $id)
            ->whereBetween('ar.shift_in', [$from, $to])
            ->orderBy('ar.shift_in', 'asc') // Sort by `shift_in` on the server side
            ->get();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $approvedLeaves = ApprovedLeave::whereBetween('date', [$from, $to])
            ->where('user_id', $id)
            ->get();
        foreach ($data as $d) {
            $start = Carbon::parse($d->shift_in);

            if ($approvedLeaves) {
                foreach ($approvedLeaves as $app) {
                    $date = Carbon::parse($app->date);
                    if ($app->start_time != null && $app->leave_type == "1" && $start->isSameDay($date)) {
                        $chdate = date('Y-m-d', strtotime($app->date));
                        $shift_in = $chdate . $app->start_time;
                        $shift_out = $chdate . $app->end_time;
                        $in = AttendanceRecord::whereDate('shift_in', $chdate)->where('user_id', $id)->first();

                        $up = AttendanceRecord::find($in->id);
                        // $up->update([
                        //     $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_in)),
                        //     $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out)),
                        // ]);
                        $color = "";
                        break;
                    } else {
                        $color = "";
                    }


                    if ($start->isSameDay($date)) {
                        $color = getColorByLeaveType($app->leave_type);
                        break;
                    } else {
                        $color = "";
                    }
                }
            }
            if ($color == "" && $d->dayoff != "Yes") {
                $currentDate = Carbon::now();
                if ($start->lte($currentDate) && !$d->check_in && !$d->check_out) {
                    $absent = "Yes";
                    if ($d->wd == "6") {
                        $i++;
                        $fine = $fine + 4;

                    } else if ($d->wd == "5") {
                        $fine = $fine + 4.8;
                        $i++;
                    }
                } else {
                    $absent = "No";
                }
            }


            $closPunchIn = null;
            $smallDifferenceIn = PHP_INT_MAX;
            $closPunchOut = null;
            $smallDifferenceOut = PHP_INT_MAX;
            $emp_id = $d->emp_id;
            preg_match('/[1-9][0-9]*/', $emp_id, $actual_id);
            $employee_id = (int) $actual_id[0];

            $checkInTime = date('Y-m-d ', strtotime($d->shift_in));
            $check1 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkInTime)->where('type', 0)->get();


            if (count($check1) > 0) {
                foreach ($check1 as $c1) {
                    $difference = abs(strtotime($d->check_in) - strtotime($c1->fingerprint_in));
                    if ($difference < $smallDifferenceIn) {
                        $smallDifferenceIn = $difference;
                        $closestPunchIn = $c1;
                    }
                }

                if (!empty($closestPunchIn) && !empty($d->check_in)) {
                    $finger_print1 = date('Y-m-d H:i:s', strtotime($closestPunchIn->fingerprint_in));
                    $fp1 = Carbon::parse($finger_print1);
                    $checkIn1 = date('Y-m-d H:i:s', strtotime($d->check_in));
                    $ch1 = Carbon::parse($checkIn1);
                    $min_dif1 = $fp1->diffInMinutes($ch1);

                    if (abs($min_dif1) <= 10) {
                        $v1 = "yes";
                    } else {
                        $v1 = "cross";
                    }
                }
            } else {
                $v1 = "no";
            }

            $checkOutTime = date('Y-m-d ', strtotime($d->shift_out));
            $check2 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkOutTime)->where('type', '1')->get();
            if (count($check2) > 0) {
                foreach ($check2 as $c2) {
                    $difference2 = abs(strtotime($d->check_out) - strtotime($c2->fingerprint_in));
                    if ($difference < $smallDifferenceOut) {
                        $smallDifferenceOut = $difference;
                        $closestPunchOut = $c2;
                    }
                }

                if (!empty($closestPunchOut) && !empty($d->check_out)) {
                    $finger_print2 = date('Y-m-d H:i:s', strtotime($closestPunchOut->fingerprint_in));
                    $fp2 = Carbon::parse($finger_print2);
                    $checkout = date('Y-m-d H:i:s', strtotime($d->check_out));
                    $co = Carbon::parse($checkout);
                    $min_dif2 = $fp2->diffInMinutes($co);
                    if (abs($min_dif2) <= 10) {
                        $v2 = "yes";
                    } else {
                        $v2 = "cross";
                    }
                }

            } else {
                $v2 = "no";
            }

            $name = $d->name;
            $start_date = date('d F Y', strtotime($d->shift_in));
            $end_date = date('d F Y', strtotime($d->shift_out));
            $shift_in = Carbon::parse($d->shift_in);
            $shift_out = Carbon::parse($d->shift_out);
            if ($d->check_in != null) {
                $in_date = date('d F Y', strtotime($d->check_in));
            } else {
                $in_date = "";
            }
            if ($d->check_out != null) {
                $out_date = date('d F Y', strtotime($d->check_out));
            } else {
                $out_date = "";
            }
            $scheduled_duration = $shift_in->diffInSeconds($shift_out);
            $total_working_duration = $scheduled_duration;

            if ($d->check_in && $d->check_out) {
                $check_in = Carbon::parse($d->check_in);
                $check_out = Carbon::parse($d->check_out);


                $scheduled_duration = $shift_in->diffInSeconds($shift_out);
                $total_working_duration = $scheduled_duration;


                if ($check_in <= $shift_in && $check_out >= $shift_out) {
                    $duty_hours = gmdate("H:i:s", $scheduled_duration);
                } else {

                    if ($shift_out->diffInHours($check_out, false) > 6) {
                        $duty_hours = "00:00:00";
                    } else {

                        if ($check_in > $shift_in) {
                            $late_duration = $shift_in->diffInSeconds($check_in);
                            $total_working_duration -= $late_duration;
                        }

                        if ($check_out < $shift_out) {
                            $early_checkout_duration = $check_out->diffInSeconds($shift_out);
                            $total_working_duration -= $early_checkout_duration;
                        }


                        $hours = floor($total_working_duration / 3600);
                        $minutes = floor(($total_working_duration % 3600) / 60);
                        $seconds = $total_working_duration % 60;

                        $duty_hours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                    }
                }
            } else {

                $duty_hours = "";
            }


            $info[] = [
                'no' => $i,
                'start_date' => $start_date,
                'name' => $d->name,
                'shift_in' => $shift_in->format('H:i:s A'),
                'shift_out' => $shift_out->format('H:i:s A'),
                'check_in' => $d->check_in ? Carbon::parse($d->check_in)->format('H:i:s A') : null,
                'check_out' => $d->check_out ? Carbon::parse($d->check_out)->format('H:i:s A') : null,
                'color' => $color,
                'duty_hours' => $duty_hours,
                'dayoff' => $d->dayoff,
                'end_date' => $end_date,
                'in_date' => $in_date,
                'out_date' => $out_date,
                'absent' => $absent,
                'verify1' => $v1,
                'verify2' => $v2,
                'id' => $d->id
            ];
        }
        $users = User::select(
            'users.id as user_id',
            'users.username',
            'users.employee_id',
            'users.week_days', // Fetch the week_days field
            DB::raw('COUNT(CASE WHEN attendance_records.dayoff = "Yes" OR attendance_records.dayoff = "PH" OR attendance_records.dayoff = "BT"  THEN 1 ELSE NULL END) as dayoff_count'),
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
            AND approved_leaves.date BETWEEN "' . $from . '" AND "' . $to . '") as leave_count')
        )
            ->leftJoin('attendance_records', function ($join) use ($from, $to) {
                $join->on('users.id', '=', 'attendance_records.user_id')
                    ->whereBetween('attendance_records.shift_in', [$from, $to]);
            })

            ->where('users.status', "1") // Include only activated users

            ->groupBy('users.id', 'users.username', 'users.employee_id', 'users.week_days')
            ->orderBy('users.id', 'asc')
            ->where('users.id', $id)
            ->get();



        $c_in = "";
        $c_out = "";
        $today = Carbon::today();
        $datetoday = date('Y-m-d', strtotime($today));
        $chj = DB::table('attendance_records')
            ->select('check_in', 'check_out')
            ->where('user_id', $id)
            ->whereDate('shift_in', $datetoday)
            ->first();

        if ($chj) {
            if ($chj->check_in != null) {
                $c_in = date('H:i:s A', strtotime($chj->check_in));
            } else {
                $c_in = "";
            }
            if ($chj->check_out != null) {
                $c_out = date('H:i:s A', strtotime($chj->check_out));
            } else {
                $c_out = "";
            }
        } else {
            $c_in = "";
            $c_out = "";
        }



        $in[] = [
            'day' => $users[0]->total_absentees,
            'absent_fine' => $users[0]->absentee_fine,
            'late_fine' => $users[0]->late_fine,
            'total' => $users[0]->absentee_fine + $users[0]->late_fine,
            'check_in' => $c_in,
            'check_out' => $c_out
        ];

        return response()->json([

            'data' => $info,
            'in' => $in

        ], 200);
    }

    public function punchIn(Request $request)
    {

        $user = auth()->user()->id;
        $checkInTime = date('Y-m-d');
        $time = date('Y-m-d H:i:s');

        $check = DB::table('attendance_records')
            ->select('*')
            ->whereDate('shift_in', '=', $checkInTime)
            ->where('check_in', '=', null)
            ->where('user_id', $user)
            ->first();

        Log::channel('checkin_checkout')->info(
            "ID: " . auth()->user()->id . "\n" .
            "Username: " . auth()->user()->username . "\n" .
            "Employee ID: " . auth()->user()->employee_id . "\n" .
            "Status: Checkin\n" .
            "Checkin Date & Time: " . $time
        );


        if (!empty($check)) {

            if (strtotime($time) > strtotime($check->shift_out)) {
                // Log error if punch-in time is after shift-out time
                Log::channel('checkin_checkout')->error("Error: User ID: $user punch-in time is after shift-out time: $time\n");
                return response()->json(['status' => 'error', 'punch_in_time' => "error"]);
            } else {
                $update = AttendanceRecord::find($check->id);
                $update->update([
                    $update->check_in = $time
                ]);

                $sess = new AttendanceSession();
                $sess->user_id = $user;
                $sess->attendance_id = $check->id;
                $sess->check_in = $time;
                $sess->save();
                // $active="on";
                // Session::put(['info'=>$sess,'status'=>$active]);


                // Log the session creation
                Log::channel('checkin_checkout')->info("Attendance session created for User ID: $user\n");

                $punch = AttendanceRecord::find($check->id);
                $punch_in_time = $punch->check_in;

                return response()->json(['status' => 'success', 'punch_in_time' => $punch_in_time]);
            }
        } else {
            // Log if no attendance record was found for the user on that day
            Log::channel('checkin_checkout')->warning("No attendance record found for User ID: $user on $checkInTime\n");
            return response()->json(['status' => 'error']);
        }

    }

    public function checkStatus()
    {
        $user = auth()->user()->id;
        $checkInTime = date('Y-m-d');
        $punch1 = AttendanceSession::where('user_id', $user)->where('check_out', NULL)->latest()->first();

        $punch = AttendanceRecord::where('user_id', $user)->where('check_out', NULL)->whereNotNull('check_in')->latest()->first();

        if ($punch && $punch1) {
            $shift_in = date('Y-m-d H:i', strtotime($punch1->check_in));

            $shift_end = date('Y-m-d H:i', strtotime($punch->shift_out));
            $start = new Carbon($shift_in);
            $end = new Carbon($shift_end);
            $duration = $start->diff($end);


            $hoursWorked = Carbon::parse($punch1->check_in)->diffInHours(Carbon::now());
            return response()->json([
                'punch_in_time' => $punch1->check_in,
                'shift_duration' => $duration->h,
                'shiftEnd' => $shift_end
            ]);
        } else {
            $check = AttendanceRecord::where('user_id', $user)->whereDate('shift_in', $checkInTime)->latest()->first();
            if ($check) {
                return response()->json(['punch_in_time' => 'show']);
            } else {
                return response()->json(['punch_in_time' => 'nothing']);
            }

        }


    }

    public function punchOut(Request $request)
    {
        $user = auth()->user()->id;

        $time = date('Y-m-d H:i:s');

        $check = AttendanceSession::where('user_id', $user)->where('check_out', NULL)->latest()->first();

        if (!empty($check)) {
            $upAttendance = AttendanceRecord::find($check->attendance_id);

            $checkInTime = $upAttendance->check_in;

            $timeDiff = strtotime($time) - strtotime($checkInTime);

            if ($timeDiff < 60) {
                return response()->json(['status' => 'error', 'message' => 'Cannot punch out Right NOw.']);
            }

            $upAttendance->update([
                $upAttendance->check_out = $time

            ]);

            // Log the successful punch-out
            Log::channel('checkin_checkout')->info(
                "ID: " . auth()->user()->id . "\n" .
                "Username: " . auth()->user()->username . "\n" .
                "Employee ID: " . auth()->user()->employee_id . "\n" .
                "Status: Checkout\n" .
                "Checkout Date & Time: " . $time
            );


            $upSession = AttendanceSession::where('user_id', $user)->get();
            $upSession->each(function ($session) {
                $session->delete();
            });

        }

        // Log if no active session found for the user to punch out
        Log::channel('checkin_checkout')->warning("No active session found for User ID: $user to punch out.\n");

        return response()->json(['status' => 'success']);
    }

    public function emergencyCheckOut()
    {
        $user = auth()->user()->id;

        $time = date('Y-m-d H:i:s');

        $check = AttendanceSession::where('user_id', $user)->where('check_out', NULL)->latest()->first();

        if (!empty($check)) {
            $upAttendance = AttendanceRecord::find($check->attendance_id);

            $checkInTime = $upAttendance->check_in;

            $timeDiff = strtotime($time) - strtotime($checkInTime);

            if ($timeDiff < 60) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Punch Out Request.']);
            }

            $upAttendance->update([
                'check_out' => $time,
                'emergency_checkout' => 1  // SAME AS PUNCHOUT TO PREVENT THE INSTANT CHECKOUT REQUEST
            ]);

            Log::channel('checkin_checkout')->info(
                "ID: " . auth()->user()->id . "\n" .
                "Username: " . auth()->user()->username . "\n" .
                "Employee ID: " . auth()->user()->employee_id . "\n" .
                "Status: Emergency Checkout\n" .
                "Checkout Date & Time: " . $time
            );

            $upSession = AttendanceSession::where('user_id', $user)->get();
            $upSession->each(function ($session) {
                $session->delete();
            });

            return response()->json(['status' => 'success', 'message' => 'Emergency Checkout Successful']);
        }
        return response()->json(['status' => 'success']);
    }


    public function statistics($fromDate = null, $toDate = null)
    {
        $info = [];
        $id = auth()->user()->id;
        if ($fromDate == null && $toDate == null) {
            $from = Carbon::now()->startOfMonth()->toDateString();
            $to = Carbon::now()->subDay();
        } else {
            $from = Carbon::parse($fromDate);
            $to = Carbon::parse($toDate);
            $from = $from->format('Y-m-d H:i:s');
            $to = $to->format('Y-m-d H:i:s');

            if ($from->diffInMonths($to) > 3) {
                return response()->json(['error' => 'The date range cannot exceed 4 months.'], 400);
            }

            if ($from->gt($to)) {
                return response()->json(['error' => 'The "From Date" cannot be greater than the "To Date".'], 400);
            }


            $currentMonthEnd = Carbon::now()->endOfMonth();
            if ($to->gt($currentMonthEnd)) {
                return response()->json(['error' => 'The "To Date" cannot exceed the current month.'], 400);
            }
            $from = $from->format('Y-m-d H:i:s');
            $to = $to->format('Y-m-d H:i:s');

        }

        $users = User::select(
            'users.id as user_id',
            'users.username',
            'users.employee_id',
            'users.week_days', // Fetch the week_days field
            DB::raw('COUNT(CASE WHEN attendance_records.dayoff = "Yes" OR attendance_records.dayoff = "PH" OR attendance_records.dayoff = "BT"   THEN 1 ELSE NULL END) as dayoff_count'),
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
            AND approved_leaves.date BETWEEN "' . $from . '" AND "' . $to . '") as leave_count')
        )
            ->leftJoin('attendance_records', function ($join) use ($from, $to) {
                $join->on('users.id', '=', 'attendance_records.user_id')
                    ->whereBetween('attendance_records.shift_in', [$from, $to]);
            })

            ->where('users.status', "1") // Include only activated users

            ->groupBy('users.id', 'users.username', 'users.employee_id', 'users.week_days')
            ->orderBy('users.id', 'asc')
            ->where('users.id', $id)
            ->get();



        $c_in = "";
        $c_out = "";
        $today = Carbon::today();
        $datetoday = date('Y-m-d', strtotime($today));
        $chj = DB::table('attendance_records')
            ->select('check_in', 'check_out')
            ->where('user_id', $id)
            ->whereDate('shift_in', $datetoday)
            ->first();

        if ($chj) {
            if ($chj->check_in != null) {
                $c_in = date('H:i:s A', strtotime($chj->check_in));
            } else {
                $c_in = "";
            }
            if ($chj->check_out != null) {
                $c_out = date('H:i:s A', strtotime($chj->check_out));
            } else {
                $c_out = "";
            }
        } else {
            $c_in = "";
            $c_out = "";
        }

        $info[] = [
            'day' => $users[0]->total_absentees,
            'absent_fine' => $users[0]->absentee_fine,
            'late_fine' => $users[0]->late_fine,
            'total' => $users[0]->absentee_fine + $users[0]->late_fine,
            'check_in' => $c_in,
            'check_out' => $c_out
        ];
        return response()->json([
            'status' => 'success',
            'info' => $info
        ]);
    }

    public function checkEmpAuhentication(Request $request)
    {
        $userData = array(
            'email' => auth()->user()->email,
            'password' => $request->password
        );
        if (Auth::attempt($userData)) {

            return response()->json([
                'status' => 'success',
            ]);

        } else {
            return response()->json([
                'status' => 'fail',
            ]);
        }

    }

    public function ediAtttendance($id)
    {
        return view('attendance.edit-attendance', compact('id'));
    }

    public function ediAtttendanceRecord($id)
    {
        $user = auth()->user();
        $permissions = getUserPermissions($user);

        $color = "";
        $info = [];
        $i = 0;
        $c_in = "";
        $c_out = "";
        $absent = "";
        $v1 = "";
        $v2 = "";

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $approvedLeaves = ApprovedLeave::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('user_id', $id)
            ->get();
        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'ar.user_id as user_id',
                'u.employee_id as emp_id',
                'ar.shift_in as shift_in',
                'ar.shift_out as shift_out',
                'ar.duty_hours as duty_hours',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff',
                'ar.id as id',
                'ar.emergency_checkout as emergency_checkout'
            )
            ->where('ar.user_id', $id)
            ->whereMonth('ar.shift_in', Carbon::now()->month)
            ->orderBy('ar.shift_in', 'asc') // Sort by `shift_in` on the server side
            ->get();

        foreach ($data as $d) {
            $start = Carbon::parse($d->shift_in);

            if ($approvedLeaves) {
                foreach ($approvedLeaves as $app) {
                    $date = Carbon::parse($app->date);
                    if ($app->start_time != null && $app->leave_type == "1" && $start->isSameDay($date)) {
                        $chdate = date('Y-m-d', strtotime($app->date));
                        $shift_in = $chdate . $app->start_time;
                        $shift_out = $chdate . $app->end_time;
                        $in = AttendanceRecord::whereDate('shift_in', $chdate)->where('user_id', $id)->first();

                        $up = AttendanceRecord::find($in->id);
                        // $up->update([
                        //     $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_in)),
                        //     $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out)),
                        // ]);
                        $color = "";
                        break;
                    } else {
                        $color = "";
                    }


                    if ($start->isSameDay($date)) {
                        $color = getColorByLeaveType($app->leave_type);
                        break;
                    } else {
                        $color = "";
                    }
                }
            }
            if ($color == "" && $d->dayoff != "Yes") {
                $currentDate = Carbon::now();
                if ($start->lte($currentDate) && !$d->check_in && !$d->check_out) {
                    $absent = "Yes";
                } else {
                    $absent = "No";
                }
            }



            $i++;
            $closPunchIn = null;
            $smallDifferenceIn = PHP_INT_MAX;
            $closPunchOut = null;
            $smallDifferenceOut = PHP_INT_MAX;
            $emp_id = $d->emp_id;
            preg_match('/[1-9][0-9]*/', $emp_id, $actual_id);
            $employee_id = (int) $actual_id[0];
            $start_date = date('d F Y', strtotime($d->shift_in));
            $end_date = date('d F Y', strtotime($d->shift_out));
            if ($d->check_in != null) {
                $in_date = date('d F Y', strtotime($d->check_in));
            } else {
                $in_date = "";
            }
            if ($d->check_out != null) {
                $out_date = date('d F Y', strtotime($d->check_out));
            } else {
                $out_date = "";
            }

            $checkInTime = date('Y-m-d ', strtotime($d->shift_in));
            $check1 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkInTime)->where('type', 0)->get();


            if (count($check1) > 0) {
                foreach ($check1 as $c1) {
                    $difference = abs(strtotime($d->check_in) - strtotime($c1->fingerprint_in));
                    if ($difference < $smallDifferenceIn) {
                        $smallDifferenceIn = $difference;
                        $closestPunchIn = $c1;
                    }
                }

                if (!empty($closestPunchIn) && !empty($d->check_in)) {
                    $finger_print1 = date('Y-m-d H:i:s', strtotime($closestPunchIn->fingerprint_in));
                    $fp1 = Carbon::parse($finger_print1);
                    $checkIn1 = date('Y-m-d H:i:s', strtotime($d->check_in));
                    $ch1 = Carbon::parse($checkIn1);
                    $min_dif1 = $fp1->diffInMinutes($ch1);

                    if (abs($min_dif1) <= 10) {
                        $v1 = "yes";
                    } else {
                        $v1 = "cross";
                    }
                }
            } else {
                $v1 = "no";
            }
            $checkOutTime = date('Y-m-d ', strtotime($d->shift_out));
            $check2 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkOutTime)->where('type', '1')->get();
            if (count($check2) > 0) {
                foreach ($check2 as $c2) {
                    $difference2 = abs(strtotime($d->check_out) - strtotime($c2->fingerprint_in));
                    if ($difference2 < $smallDifferenceOut) {
                        $smallDifferenceOut = $difference2;
                        $closestPunchOut = $c2;
                    }
                }

                if (!empty($closestPunchOut) && !empty($d->check_out)) {
                    $finger_print2 = date('Y-m-d H:i:s', strtotime($closestPunchOut->fingerprint_in));
                    $fp2 = Carbon::parse($finger_print2);
                    $checkout = date('Y-m-d H:i:s', strtotime($d->check_out));
                    $co = Carbon::parse($checkout);
                    $min_dif2 = $fp2->diffInMinutes($co);
                    if (abs($min_dif2) <= 10) {
                        $v2 = "yes";
                    } else {
                        $v2 = "cross";
                    }
                }

            } else {
                $v2 = "no";
            }

            $name = $d->name;

            $shift_in = Carbon::parse($d->shift_in);
            $shift_out = Carbon::parse($d->shift_out);
            $scheduled_duration = $shift_in->diffInSeconds($shift_out);

            if ($d->check_in && $d->check_out) {
                $check_in = Carbon::parse($d->check_in);
                $check_out = Carbon::parse($d->check_out);

                // Calculate effective check-in and check-out within shift boundaries
                $effective_check_in = $check_in->lt($shift_in) ? $shift_in : $check_in;
                $effective_check_out = $check_out->gt($shift_out) ? $shift_out : $check_out;

                // Calculate the working duration in seconds
                $working_duration = $effective_check_in->diffInSeconds($effective_check_out);

                // Convert the working duration into hours, minutes, and seconds
                $hours = floor($working_duration / 3600);
                $minutes = floor(($working_duration % 3600) / 60);
                $seconds = $working_duration % 60;

                $duty_hours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            } else {
                // Default value if check-in or check-out is missing
                $duty_hours = "";
            }



            $info[] = [
                'no' => $i,
                'start_date' => $start_date,
                'name' => $d->name,
                'shift_in' => $shift_in->format('H:i:s'),
                'shift_out' => $shift_out->format('H:i:s'),
                'check_in' => $d->check_in ? Carbon::parse($d->check_in)->format('H:i:s') : null,
                'check_out' => $d->check_out ? Carbon::parse($d->check_out)->format('H:i:s') : null,
                'color' => $color,
                'duty_hours' => $duty_hours,
                'dayoff' => $d->dayoff,
                'end_date' => $end_date,
                'in_date' => $in_date,
                'out_date' => $out_date,
                'absent' => $absent,
                'verify1' => $v1,
                'verify2' => $v2,
                'id' => $d->id,
                'emergency_checkout' => $d->emergency_checkout
            ];
        }



        return response()->json([

            'data' => $info,
            'permissions' => [
                'can_edit' => in_array('update_attendance_schedule', $permissions),
                'can_delete' => in_array('delete_attendance_schedule', $permissions),
            ],
        ], 200);

    }
    public function deleteAttendance(Request $request)
    {
        // Get the authenticated user details
        $authId = auth()->user();
        $serverTime = now();

        $ids = $request->input('ids');

        // Initialize the logger
        $logger = app('log')->channel('delete_schedule');

        // Log the incoming request data (user who requested the delete and the IDs to delete)
        $logger->info("Schedule Deleted By: User ID: {$authId->id}, Username: {$authId->username}, Employee ID: {$authId->employee_id}");

        // Fetch all the attendance records that match the provided IDs
        $attendanceRecords = AttendanceRecord::whereIn('id', $ids)->get();

        // If no records are found, log that and return an error message
        if ($attendanceRecords->isEmpty()) {
            $logger->warning("No attendance records found for IDs: " . implode(', ', $ids));
            return response()->json(['message' => 'No records found for deletion.'], 404);
        }

        // Fetch the leader details only once, as they are the same for all records
        $leaderId = $attendanceRecords->first()->leader_id;
        $fetchLeader = User::find($leaderId);
        $LeaderName = $fetchLeader->username;
        $LeaderEMPID = $fetchLeader->employee_id;

        // Log the leader's information once
        $logger->info("Leader ID: {$leaderId}, Username: {$LeaderName}, Employee ID: {$LeaderEMPID}");

        // Loop through each attendance record and log the user details
        foreach ($attendanceRecords as $attendanceRecord) {
            $userId = $attendanceRecord->user_id;
            $shiftIn = $attendanceRecord->shift_in;
            $shiftOut = $attendanceRecord->shift_out;

            // Fetch user details for each record
            $fetchUser = User::find($userId);
            $EmpName = $fetchUser->username;
            $EmpID = $fetchUser->employee_id;

            // Log the details of the attendance record, including Shift In and Shift Out
            $logger->info("Attendance Record ID: {$attendanceRecord->id} | Shift In: {$shiftIn} | Shift Out: {$shiftOut} | User ID: {$userId}, Username: {$EmpName}, Employee ID: {$EmpID}");
        }

        try {

            AttendanceRecord::whereIn('id', $ids)->delete();

            // Log the success of the operation
            $logger->info("Timestamp: {$serverTime}");
            $logger->info("Schedule Deleted successfully.\n");

            return response()->json(['message' => 'Records deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the error if deletion fails
            $logger->error("Error deleting attendance records: " . $e->getMessage() . "\n");

            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
    public function deleteSingleAttendance(Request $request)
    {
        // Get the authenticated user details
        $authId = auth()->user();
        $serverTime = now();

        $id = $request->input('id');

        $fetchData = AttendanceRecord::find($id);
        $leaderId = $fetchData->leader_id;  // Assuming leader_id exists in the attendance table
        $userId = $fetchData->user_id;
        $shiftIn = $fetchData->shift_in;
        $shiftout = $fetchData->shift_out;

        $fetchUser = User::find($userId);
        $fetchLeader = User::find($leaderId);
        $EmpName = $fetchUser->username;
        $EmpID = $fetchUser->employee_id;
        $LeaderName = $fetchLeader->username;
        $LeaderEMPID = $fetchLeader->employee_id;


        // Set up the logger
        $logger = app('log')->channel('delete_schedule');
        $logger->info("Single Attendance Record Deleted By: User ID: {$authId->id}, Username: {$authId->username}, Employee ID: {$authId->employee_id}");

        // Log the attempt to delete the record with the provided ID
        $logger->info("User Record Deleted: User ID: {$userId}, Username:{$EmpName}, Employee ID: {$EmpID}");
        $logger->info("Leader ID: {$leaderId}, Username:{$LeaderName}, Employee ID:{$LeaderEMPID}");
        $logger->info("Record ID: {$id} | Shift In: {$shiftIn} | Shift Out: {$shiftout}");
        // $logger->info("Shift In: {$shiftIn}\nShift Out: {$shiftout}");
        $logger->info("Timestamp: {$serverTime}");

        if (empty($id)) {
            return response()->json(['message' => 'No ID provided.'], 400);
        }

        try {

            $deleted = AttendanceRecord::where('id', $id)->delete();

            if ($deleted) {
                $logger->info("Record Deleted Successfully.\n");
                return response()->json(['message' => 'Record deleted successfully.'], 200);
            } else {
                return response()->json(['message' => 'Record not found.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
    public function getSchedule($id)
    {
        // Find the schedule by ID
        $schedule = AttendanceRecord::find($id);

        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        // Format dates and times, handling null values gracefully
        $start_date = $schedule->shift_in ? date('Y-m-d\TH:i', strtotime($schedule->shift_in)) : null;
        $end_date = $schedule->shift_out ? date('Y-m-d\TH:i', strtotime($schedule->shift_out)) : null;
        $check_in = $schedule->check_in ? date('Y-m-d\TH:i', strtotime($schedule->check_in)) : null;
        $check_out = $schedule->check_out ? date('Y-m-d\TH:i', strtotime($schedule->check_out)) : null;

        return response()->json([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'dayoff' => $schedule->dayoff,
            'emergency_checkout' => $schedule->emergency_checkout,
        ]);
    }

    public function attendanceRecordEdit(Request $request)
    {
        $id = auth()->user()->id;

        $user = auth()->user();
        $permissions = getUserPermissions($user);

        $allowed_offices = ['Sihanoukville', 'Malaysia', 'Bavet', 'Poipet', 'TWFM'];
        $matching_offices = array_intersect($allowed_offices, $permissions);

        if (!empty($matching_offices)) {
            $office_based_permission = array_values($matching_offices);
        } else {
            $office_based_permission = [];
        }

        // dd($office_based_permission);
        if (auth()->user()->role == "1" || auth()->user()->role == "3") {
            $query = DB::table('users as u')
                ->join('departments as d', 'u.department_id', '=', 'd.id')
                ->select('u.id as id', 'u.employee_id as employee_id', 'u.username as username', 'd.name as department')
                ->where('u.status', '=', '1');

        } elseif (($user->role == 2 || $user->role == 3) && $office_based_permission) {
            $query = DB::table('users as u')
                ->join('departments as d', 'u.department_id', '=', 'd.id')
                ->join('user_profiles as up', 'u.id', '=', 'up.user_id')
                ->select('u.id as id', 'u.employee_id as employee_id', 'u.username as username', 'd.name as department')
                ->whereIn('u.role', [2, 4, 5])
                ->where('u.status', '=', '1')
                ->whereIn('up.office', $office_based_permission);
        } else {
            $query = DB::table('users as u')
                ->join('departments as d', 'u.department_id', '=', 'd.id')
                ->join('leader_employees as ld', 'ld.employee_id', '=', 'u.id')
                ->select('u.id as id', 'u.employee_id as employee_id', 'u.username as username', 'd.name as department')
                ->where('ld.leader_id', '=', $id)
                ->where('u.status', '=', '1');
        }
        // Apply search condition if a search term is provided
        if ($request->has('search') && $request->input('search')['value']) {
            $search = $request->input('search')['value'];

            // Split the search term into multiple parts (e.g., first name, last name)
            $searchParts = explode(' ', $search);

            $query->where(function ($query) use ($searchParts) {
                // If more than one search term, ensure all terms match across fields
                foreach ($searchParts as $part) {
                    $query->where(function ($query) use ($part) {
                        $query->where('u.username', 'like', "%$part%")
                            ->orWhere('u.employee_id', 'like', "%$part%")
                            ->orWhere('u.email', 'like', "%$part%");
                    });
                }
            });
        }


        // Add distinct() to ensure we don't get duplicate records
        $query->distinct();



        $perPage = $request->input('length', 10);
        $currentPage = ($request->input('start', 0) / $perPage) + 1;

        $users = $query->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $users->items(),
        ]);
    }
    public function empployeeList()
    {
        return view('attendance.employee-list');
    }

    public function updateAttendance(Request $request)
    {
        // OBTAINING PERMISSIONS 
        $user = auth()->user();
        $serverTime = now();
        $permissions = getUserPermissions($user);
        // PERMISSIONS 
        $shift_in = date('Y-m-d H:i', strtotime($request->startdate[0] . ' ' . $request->start_time));
        $shift_out = date('Y-m-d H:i', strtotime($request->enddate[0] . ' ' . $request->end_time));
        $chk_in = $request->check_in_datetime ? date('Y-m-d H:i', strtotime($request->check_in_datetime)) : null;
        $chk_out = $request->check_out_datetime ? date('Y-m-d H:i', strtotime($request->check_out_datetime)) : null;

        $id = (int) $request->row_id;
        $schedule = AttendanceRecord::find($id);

        $logger = app(UpdateScheduleLog::class);
        $logger = $logger($shift_in);

        $logger->info("Schedule Updated By:User ID: {$user->id}, Username: {$user->username}, Employee ID: {$user->employee_id}\n");

        if ($schedule) {

            // Fetch the leader details using DB
            $leader = DB::table('users')->select('id', 'username', 'employee_id')
                ->where('id', $schedule->leader_id)
                ->first();

            // Fetch the user details using DB
            $userSchedule = DB::table('users')->select('id', 'username', 'employee_id')
                ->where('id', $schedule->user_id)
                ->first();

            // Log the old schedule (before updating)
            $logger->info("Old Data:Schedule ID: {$schedule->id}\nLeader ID: {$leader->id}, Leader Username: {$leader->username}, Leader Employee ID: {$leader->employee_id}\nSchedule Updated For: User ID: {$userSchedule->id}, User Username: {$userSchedule->username}, User Employee ID: {$userSchedule->employee_id}");
            $logger->info("Old Check-in/Check-out Time:\nCheck-in: {$schedule->check_in}, Check-out: {$schedule->check_out}");
            $logger->info("Old Shift Time:\nShift-in: {$schedule->shift_in}, Shift-out: {$schedule->shift_out}");

            // Log the old Manage Day (dayoff) value
            switch ($schedule->dayoff) {
                case 'Yes':
                    $logger->info("Old Manage Day: Off Day\n");
                    break;
                case 'No':
                    $logger->info("Old Manage Day: Working Day\n");
                    break;
                case 'BT':
                    $logger->info("Old Manage Day: Business Trip\n");
                    break;
                case 'PH':
                    $logger->info("Old Manage Day: Public Holiday\n");
                    break;
                default:
                    $logger->info("Old Manage Day: Unknown\n");
                    break;
            }

            if ($user->role === "1" || in_array('edit_checkin_checkout', $permissions)) {
                $schedule->check_in = $chk_in;
                $schedule->check_out = $chk_out;
            }
            $schedule->shift_in = $shift_in;
            $schedule->shift_out = $shift_out;
            $schedule->dayoff = $request->manage_day;

            $schedule->save();


            // Log the updated schedule with the new values
            $logger->info("New Data:Schedule ID: {$schedule->id}\nLeader ID: {$leader->id}, Leader Username: {$leader->username}, Leader Employee ID: {$leader->employee_id}\nSchedule Updated For: User ID: {$userSchedule->id}, User Username: {$userSchedule->username}, User Employee ID: {$userSchedule->employee_id}");
            $logger->info("Updated Check-in/Check-out Time:\nCheck-in: {$chk_in}, Check-out: {$chk_out}");
            $logger->info("Updated Shift Time:\nShift-in: {$shift_in}, Shift-out: {$shift_out}");

            // Log the updated manage day (dayoff) using switch
            switch ($schedule->dayoff) {
                case 'Yes':
                    $logger->info("Updated Manage Day: Off Day\n");
                    break;
                case 'No':
                    $logger->info("Updated Manage Day: Working Day\n");
                    break;
                case 'BT':
                    $logger->info("Updated Manage Day: Business Trip\n");
                    break;
                case 'PH':
                    $logger->info("Updated Manage Day: Public Holiday\n");
                    break;
                default:
                    $logger->info("Updated Manage Day: Unknown\n");
                    break;
            }

            // Log success
            $logger->info("Timestamp: {$serverTime}.");
            $logger->info("Schedule Updated Successfully.\n");


            return response()->json(['success' => true]);
        } else {
            // Log failure if schedule not found
            $logger->warning("Schedule not found for ID: $id\n");

            return response()->json(['success' => false, 'message' => 'Schedule not found.']);
        }
    }

    public function statisticsAdmin($id)
    {
        $info = [];
        if ($id == null) {
            $id = auth()->user()->id;
        }


        $from = Carbon::now()->startOfMonth()->toDateString();
        $to = Carbon::now()->subDay();


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
            AND approved_leaves.date BETWEEN "' . $from . '" AND "' . $to . '") as leave_count')
        )
            ->leftJoin('attendance_records', function ($join) use ($from, $to) {
                $join->on('users.id', '=', 'attendance_records.user_id')
                    ->whereBetween('attendance_records.shift_in', [$from, $to]);
            })

            ->where('users.status', "1") // Include only activated users

            ->groupBy('users.id', 'users.username', 'users.employee_id', 'users.week_days')
            ->orderBy('users.id', 'asc')
            ->where('users.id', $id)
            ->get();



        $c_in = "";
        $c_out = "";
        $today = Carbon::today();
        $datetoday = date('Y-m-d', strtotime($today));
        $chj = DB::table('attendance_records')
            ->select('check_in', 'check_out')
            ->where('user_id', $id)
            ->whereDate('shift_in', $datetoday)
            ->first();

        if ($chj) {
            if ($chj->check_in != null) {
                $c_in = date('H:i:s A', strtotime($chj->check_in));
            } else {
                $c_in = "";
            }
            if ($chj->check_out != null) {
                $c_out = date('H:i:s A', strtotime($chj->check_out));
            } else {
                $c_out = "";
            }
        } else {
            $c_in = "";
            $c_out = "";
        }

        $info[] = [
            'day' => $users[0]->total_absentees,
            'absent_fine' => $users[0]->absentee_fine,
            'late_fine' => $users[0]->late_fine,
            'total' => $users[0]->absentee_fine + $users[0]->late_fine,
            'check_in' => $c_in,
            'check_out' => $c_out
        ];

        return response()->json([
            'status' => 'success',
            'info' => $info
        ]);
    }
    public function searchAdmin(Request $request)
    {
        $color = "";
        $id = (int) $request->id;
        $absent = "";
        $color = "";
        $info = [];
        $i = 0;
        $c_in = "";
        $c_out = "";
        $absent = "";
        $difference = 0;
        $v1 = "";
        $v2 = "";
        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);


        if ($from->diffInMonths($to) > 3) {
            return response()->json(['error' => 'The date range cannot exceed 4 months.'], 400);
        }

        if ($from->gt($to)) {
            return response()->json(['error' => 'The "From Date" cannot be greater than the "To Date".'], 400);
        }

        $from = $from->format('Y-m-d H:i:s');
        $to = $to->format('Y-m-d H:i:s');

        $info = [];
        $i = 0;
        $total_late_minutes = 0;
        $stotal_late_minutes = 0;
        $total_penalty = 0.0;

        $fine = 0.0;
        $late_minutes = 0;
        $slate_minutes = 0;
        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'u.week_days as wd',
                'ar.user_id as user_id',
                'u.employee_id as emp_id',
                'ar.shift_in as shift_in',
                'ar.id as id',
                'ar.shift_out as shift_out',
                'ar.duty_hours as duty_hours',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff',
                'ar.id as id'
            )
            ->where('ar.user_id', $id)
            ->whereBetween('ar.shift_in', [$from, $to])
            ->orderBy('ar.shift_in', 'asc') // Sort by `shift_in` on the server side
            ->get();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $approvedLeaves = ApprovedLeave::whereBetween('date', [$from, $to])
            ->where('user_id', $id)
            ->whereNull('start_time')
            ->get();
        foreach ($data as $d) {
            $start = Carbon::parse($d->shift_in);

            if ($approvedLeaves) {
                foreach ($approvedLeaves as $app) {
                    $date = Carbon::parse($app->date);
                    if ($app->start_time != null && $app->leave_type == "1" && $start->isSameDay($date)) {
                        $chdate = date('Y-m-d', strtotime($app->date));
                        $shift_in = $chdate . $app->start_time;
                        $shift_out = $chdate . $app->end_time;
                        $in = AttendanceRecord::whereDate('shift_in', $chdate)->where('user_id', $id)->first();

                        $color = "";
                        break;
                    } else {
                        $color = "";
                    }


                    if ($start->isSameDay($date)) {
                        $color = getColorByLeaveType($app->leave_type);
                        break;
                    } else {
                        $color = "";
                    }
                }
            }
            if ($color == "" && $d->dayoff != "Yes") {
                $currentDate = Carbon::now();
                if ($start->lte($currentDate) && !$d->check_in && !$d->check_out) {
                    $absent = "Yes";
                    if ($d->wd == "6") {
                        $i++;
                        $fine = $fine + 4;

                    } else if ($d->wd == "5") {
                        $fine = $fine + 4.8;
                        $i++;
                    }
                } else {
                    $absent = "No";
                }
            }


            $closPunchIn = null;
            $smallDifferenceIn = PHP_INT_MAX;
            $closPunchOut = null;
            $smallDifferenceOut = PHP_INT_MAX;
            $emp_id = $d->emp_id;
            preg_match('/[1-9][0-9]*/', $emp_id, $actual_id);
            $employee_id = (int) $actual_id[0];

            $checkInTime = date('Y-m-d ', strtotime($d->shift_in));
            $check1 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkInTime)->where('type', 0)->get();


            if (count($check1) > 0) {
                foreach ($check1 as $c1) {
                    $difference = abs(strtotime($d->check_in) - strtotime($c1->fingerprint_in));
                    if ($difference < $smallDifferenceIn) {
                        $smallDifferenceIn = $difference;
                        $closestPunchIn = $c1;
                    }
                }

                if (!empty($closestPunchIn) && !empty($d->check_in)) {
                    $finger_print1 = date('Y-m-d H:i:s', strtotime($closestPunchIn->fingerprint_in));
                    $fp1 = Carbon::parse($finger_print1);
                    $checkIn1 = date('Y-m-d H:i:s', strtotime($d->check_in));
                    $ch1 = Carbon::parse($checkIn1);
                    $min_dif1 = $fp1->diffInMinutes($ch1);

                    if (abs($min_dif1) <= 10) {
                        $v1 = "yes";
                    } else {
                        $v1 = "cross";
                    }
                }
            } else {
                $v1 = "no";
            }






            $checkOutTime = date('Y-m-d ', strtotime($d->shift_out));
            $check2 = DB::table('check_verify')
                ->where('user_id', $employee_id)
                ->whereDate('fingerprint_in', $checkOutTime)->where('type', '1')->get();
            if (count($check2) > 0) {
                foreach ($check2 as $c2) {
                    $difference2 = abs(strtotime($d->check_out) - strtotime($c2->fingerprint_in));
                    if ($difference < $smallDifferenceOut) {
                        $smallDifferenceOut = $difference;
                        $closestPunchOut = $c2;
                    }
                }

                if (!empty($closestPunchOut) && !empty($d->check_out)) {
                    $finger_print2 = date('Y-m-d H:i:s', strtotime($closestPunchOut->fingerprint_in));
                    $fp2 = Carbon::parse($finger_print2);
                    $checkout = date('Y-m-d H:i:s', strtotime($d->check_out));
                    $co = Carbon::parse($checkout);
                    $min_dif2 = $fp2->diffInMinutes($co);
                    if (abs($min_dif2) <= 10) {
                        $v2 = "yes";
                    } else {
                        $v2 = "cross";
                    }
                }

            } else {
                $v2 = "no";
            }

            $name = $d->name;
            $start_date = date('d F Y', strtotime($d->shift_in));
            $end_date = date('d F Y', strtotime($d->shift_out));
            $shift_in = Carbon::parse($d->shift_in);
            $shift_out = Carbon::parse($d->shift_out);
            if ($d->check_in != null) {
                $in_date = date('d F Y', strtotime($d->check_in));
            } else {
                $in_date = "";
            }
            if ($d->check_out != null) {
                $out_date = date('d F Y', strtotime($d->check_out));
            } else {
                $out_date = "";
            }
            $scheduled_duration = $shift_in->diffInSeconds($shift_out);
            $total_working_duration = $scheduled_duration;

            if ($d->check_in && $d->check_out) {
                $check_in = Carbon::parse($d->check_in);
                $check_out = Carbon::parse($d->check_out);


                $scheduled_duration = $shift_in->diffInSeconds($shift_out);
                $total_working_duration = $scheduled_duration;


                if ($check_in <= $shift_in && $check_out >= $shift_out) {
                    $duty_hours = gmdate("H:i:s", $scheduled_duration);
                } else {

                    if ($shift_out->diffInHours($check_out, false) > 6) {
                        $duty_hours = "00:00:00";
                    } else {

                        if ($check_in > $shift_in) {
                            $late_duration = $shift_in->diffInSeconds($check_in);
                            $total_working_duration -= $late_duration;
                        }

                        if ($check_out < $shift_out) {
                            $early_checkout_duration = $check_out->diffInSeconds($shift_out);
                            $total_working_duration -= $early_checkout_duration;
                        }


                        $hours = floor($total_working_duration / 3600);
                        $minutes = floor(($total_working_duration % 3600) / 60);
                        $seconds = $total_working_duration % 60;

                        $duty_hours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                    }
                }
            } else {

                $duty_hours = "";
            }


            $info[] = [
                'no' => $i,
                'start_date' => $start_date,
                'name' => $d->name,
                'shift_in' => $shift_in->format('H:i:s A'),
                'shift_out' => $shift_out->format('H:i:s A'),
                'check_in' => $d->check_in ? Carbon::parse($d->check_in)->format('H:i:s A') : null,
                'check_out' => $d->check_out ? Carbon::parse($d->check_out)->format('H:i:s A') : null,
                'color' => $color,
                'duty_hours' => $duty_hours,
                'dayoff' => $d->dayoff,
                'end_date' => $end_date,
                'in_date' => $in_date,
                'out_date' => $out_date,
                'absent' => $absent,
                'verify1' => $v1,
                'verify2' => $v2,
                'id' => $d->id
            ];
        }
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
            AND approved_leaves.date BETWEEN "' . $from . '" AND "' . $to . '") as leave_count')
        )
            ->leftJoin('attendance_records', function ($join) use ($from, $to) {
                $join->on('users.id', '=', 'attendance_records.user_id')
                    ->whereBetween('attendance_records.shift_in', [$from, $to]);
            })

            ->where('users.status', "1") // Include only activated users

            ->groupBy('users.id', 'users.username', 'users.employee_id', 'users.week_days')
            ->orderBy('users.id', 'asc')
            ->where('users.id', $id)
            ->get();



        $c_in = "";
        $c_out = "";
        $today = Carbon::today();
        $datetoday = date('Y-m-d', strtotime($today));
        $chj = DB::table('attendance_records')
            ->select('check_in', 'check_out')
            ->where('user_id', $id)
            ->whereDate('shift_in', $datetoday)
            ->first();

        if ($chj) {
            if ($chj->check_in != null) {
                $c_in = date('H:i:s A', strtotime($chj->check_in));
            } else {
                $c_in = "";
            }
            if ($chj->check_out != null) {
                $c_out = date('H:i:s A', strtotime($chj->check_out));
            } else {
                $c_out = "";
            }
        } else {
            $c_in = "";
            $c_out = "";
        }



        $in[] = [
            'day' => $users[0]->total_absentees,
            'absent_fine' => $users[0]->absentee_fine,
            'late_fine' => $users[0]->late_fine,
            'total' => $users[0]->absentee_fine + $users[0]->late_fine,
            'check_in' => $c_in,
            'check_out' => $c_out
        ];

        return response()->json([

            'data' => $info,
            'in' => $in

        ], 200);
    }
}