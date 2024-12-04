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
    FingerPrint,
    ApprovedLeave
};

class AttendanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $id = auth()->user()->id;
        $fourMonth = Carbon::now()->subMonths(4)->startOfMonth();
        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'ar.user_id as user_id',
                'ar.shift_in as shift_in',
                'ar.id as id',
                'ar.shift_out as shift_out',
                'ar.duty_hours as duty_hours',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff'
            )
            ->where('ar.leader_id', $id)
            ->where('ar.shift_in', '>=', $fourMonth)
            ->get()
            ->groupBy('ar.user_id');
        dd($data);

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
                        $up->update([
                            $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_in)),
                            $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out))
                        ]);
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

                if (!empty($closestPunchIn)) {
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

                if (!empty($closestPunchOut)) {
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
        return view('attendance.attendance-employee');
    }

    public function searchRecord(Request $request)
    {
        $id = auth()->user()->id;
        $absent = "";
        $color = "";
        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);


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

        $info = [];
        $i = 0;
        $total_late_minutes = 0;
        $total_penalty = 0.0;

        $fine = 0.0;
        $late_minutes = 0;
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
                        $up->update([
                            $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_in)),
                            $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out)),
                        ]);
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

                if (!empty($closestPunchIn)) {
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


            if ($d->check_in && $d->check_out && $d->shift_in && $d->shift_out) {

                $shift_in = Carbon::parse($d->shift_in);
                $shift_out = Carbon::parse($d->shift_out);
                $check_in = Carbon::parse($d->check_in);
                $check_out = Carbon::parse($d->check_out);


                if (
                    (($check_out->format('A') == 'AM' && $shift_out->format('A') == 'PM') ||
                        ($check_out->format('A') == 'PM' && $shift_out->format('A') == 'AM')) &&
                    abs($check_out->diffInHours($shift_out)) > 6
                ) {


                    if (auth()->user()->week_days == "6") {
                        $i++;
                        $fine = $fine + 4;
                    } else if (auth()->user()->week_days == "5") {
                        $fine = $fine + 4.8;
                        $i++;
                    }
                } else {


                    if ($check_in->greaterThan($shift_in)) {
                        $late_minutes += abs($shift_in->diffInMinutes($check_in));
                        $i++;

                    }

                    if ($check_out->lessThan($shift_out)) {
                        $late_minutes += abs($shift_out->diffInMinutes($check_out));
                        $i++;
                    }

                    $total_late_minutes += $late_minutes;

                }

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

                if (!empty($closestPunchOut)) {
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

        $hours = floor($late_minutes / 60);
        $minutes = $late_minutes % 60;




        if (auth()->user()->week_days == "6") {
            if ($late_minutes > 0 && $late_minutes <= 15) {
                $total_penalty += 0.125;
            } elseif ($late_minutes > 15 && $late_minutes <= 540) {
                $range_index = ceil(($late_minutes - 15) / 15);
                $total_penalty += 0.125 * $range_index;
            }

        }
        if (auth()->user()->week_days == "5") {
            if ($late_minutes > 0 && $late_minutes <= 15) {
                $total_penalty += 0.25;
            } elseif ($late_minutes > 15 && $late_minutes <= 540) {
                $range_index = ceil(($late_minutes - 15) / 15);
                $total_penalty += 0.25 * $range_index;
            }
        }


        $deduction = $total_penalty + $fine;
        $in[] = [
            'day' => $i,
            'hour' => $hours,
            'minute' => $minutes,
            'deduction' => $deduction,
            'abc' => $total_penalty
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
            ->select('id')
            ->whereDate('shift_in', '=', $checkInTime)
            ->where('check_in', '=', null)
            ->where('user_id', $user)
            ->first();

        if (!empty($check)) {

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

            $punch = AttendanceRecord::find($check->id);
            $punch_in_time = $punch->check_in;

            return response()->json(['status' => 'success', 'punch_in_time' => $punch_in_time]);
        } else {
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
            $upAttendance->update([
                $upAttendance->check_out = $time

            ]);

            $upSession = AttendanceSession::find($check->id);
            $upSession->delete();




        }
        return response()->json(['status' => 'success']);
    }

    public function statistics($fromDate = null, $toDate = null)
    {
        $info = [];

        $id = auth()->user()->id;

        $color = "";
        $total_late_minutes = 0;
        $total_penalty = 0.0;
        $i = 0;
        $c_in = "";
        $c_out = "";
        $fine = 0.0;
        $late_minutes = 0;
        $today = Carbon::today();
        $datetoday = date('Y-m-d', strtotime($today));
        $chj = DB::table('attendance_records')
            ->select('check_in', 'check_out')
            ->where('user_id', $id)
            ->whereDate('shift_in', $datetoday)
            ->first();

        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.week_days as wd',
                'ar.user_id as user_id',
                'ar.shift_in as shift_in',
                'ar.shift_out as shift_out',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff'
            )
            ->where('ar.user_id', $id)
            ->whereMonth('ar.shift_in', Carbon::now()->month)
            ->get();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $approvedLeaves = ApprovedLeave::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('user_id', $id)
            ->get();


        foreach ($data as $record) {
            $start = Carbon::parse($record->shift_in);

            if ($approvedLeaves) {
                foreach ($approvedLeaves as $app) {
                    $date = Carbon::parse($app->date);
                    if ($app->start_time != null && $app->leave_type == "1" && $start->isSameDay($date)) {
                        $chdate = date('Y-m-d', strtotime($app->date));
                        $shift_in = $chdate . $app->start_time;
                        $shift_out = $chdate . $app->end_time;
                        $in = AttendanceRecord::whereDate('shift_in', $chdate)->where('user_id', $id)->first();

                        $up = AttendanceRecord::find($in->id);
                        $up->update([
                            $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_out)),
                            $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out)),
                        ]);
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
            if ($color == "" && $record->dayoff != "Yes") {

                $currentDate = Carbon::now();
                if ($start->lte($currentDate) && !$record->check_in && !$record->check_out) {
                    $i++;
                    if ($record->wd == "6") {
                        $fine = $fine + 4;

                    } else if ($record->wd == "5") {
                        $fine = $fine + 4.8;
                    }
                }
            }




            if ($record->check_in && $record->check_out && $record->shift_in && $record->shift_out && $color == "" && $record->dayoff != "Yes") {

                $shift_in = Carbon::parse($record->shift_in);
                $shift_out = Carbon::parse($record->shift_out);
                $check_in = Carbon::parse($record->check_in);
                $check_out = Carbon::parse($record->check_out);


                if (
                    (($check_out->format('A') == 'AM' && $shift_out->format('A') == 'PM') ||
                        ($check_out->format('A') == 'PM' && $shift_out->format('A') == 'AM')) &&
                    abs($check_out->diffInHours($shift_out)) > 6
                ) {

                    $i++;
                    if ($record->wd == "6") {
                        $fine = $fine + 4;
                    } else if ($record->wd == "5") {
                        $fine = $fine + 4.8;
                    }
                } else {

                    if ($check_in->greaterThan($shift_in)) {
                        $late_minutes += abs($check_in->diffInMinutes($shift_in));

                        $i++;
                    }

                    if ($check_out->lessThan($shift_out)) {
                        $late_minutes += abs($shift_out->diffInMinutes($check_out));
                        $i++;
                    }

                    // $penalty = ceil($late_minutes / 15) * 0.125;
                    // $total_penalty += $penalty;

                    $total_late_minutes += $late_minutes;

                }

            }
        }


        $hour = floor($late_minutes / 60);
        $minute = $late_minutes % 60;


        if (auth()->user()->week_days == "6") {
            if ($total_late_minutes > 0 && $total_late_minutes <= 15) {
                $total_penalty += 0.125;
            } elseif ($late_minutes > 15 && $total_late_minutes <= 540) {
                $range_index = ceil(($total_late_minutes - 15) / 15);
                $total_penalty += 0.125 * $range_index;
            }

        }
        if (auth()->user()->week_days == "5") {
            if ($total_late_minutes > 0 && $total_late_minutes <= 15) {
                $total_penalty += 0.25;
            } elseif ($late_minutes > 15 && $total_late_minutes <= 540) {
                $range_index = ceil(($total_late_minutes - 15) / 15);
                $total_penalty += 0.25 * $range_index;
            }
        }


        $deduction = $total_penalty + $fine;
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
            'day' => $i,
            'hour' => $hour,
            'minute' => $minute,
            'deduction' => $deduction,
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
        $color = "";
        $info = [];
        $i = 0;
        $c_in = "";
        $c_out = "";
        $absent = "";

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
                'ar.id as id'
            )
            ->where('ar.user_id', $id)
            ->whereMonth('ar.shift_in', Carbon::now()->month)
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
                        $up->update([
                            $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_out)),
                            $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out)),
                        ]);
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

                if (!empty($closestPunchIn)) {
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

                if (!empty($closestPunchOut)) {
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
                'id' => $d->id
            ];
        }



        return response()->json([

            'data' => $info,
        ], 200);

    }
    public function deleteAttendance(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided.'], 400);
        }

        try {

            AttendanceRecord::whereIn('id', $ids)->delete();
            return response()->json(['message' => 'Records deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
    public function deleteSingleAttendance(Request $request)
    {
        $id = $request->input('id');

        if (empty($id)) {
            return response()->json(['message' => 'No ID provided.'], 400);
        }

        try {

            $deleted = AttendanceRecord::where('id', $id)->delete();

            if ($deleted) {
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
        $schedule = AttendanceRecord::find($id);
        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        return response()->json([

            'start_date' => date('Y-m-d', strtotime($schedule->shift_in)),
            'start_time' => date('H:i A', strtotime($schedule->shift_in)),
            'end_date' => date('Y-m-d', strtotime($schedule->shift_out)),
            'end_time' => date('H:i A', strtotime($schedule->shift_out)),
        ]);
    }

    public function attendanceRecordEdit()
    {

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $employeeIds = DB::table('attendance_records')
            ->whereBetween('shift_in', [$currentMonthStart, $currentMonthEnd])
            ->pluck('user_id')
            ->unique();

        $users = DB::table('users as u')
            ->join('departments as d', 'd.id', '=', 'u.department_id')
            ->select('u.id as id', 'u.employee_id as emp_id', 'u.username as username', 'd.name as department')
            ->whereIn('u.id', $employeeIds)
            ->paginate(10);



        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'emp_id' => $user->emp_id,
                'name' => $user->username,
                'department' => $user->department,

            ];
        }

        return response()->json([
            'data' => $data,
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
        ]);

    }
    public function empployeeList()
    {
        return view('attendance.employee-list');
    }

    public function updateAttendance(Request $request)
    {

        $shift_start = $request->startdate[0] . $request->start_time;
        $shift_in = date('Y-m-d H:i', strtotime($shift_start));
        $shift_end = $request->enddate[0] . $request->end_time;
        $shift_out = date('Y-m-d H:i', strtotime($shift_end));
        $id = (int) $request->row_id;
        $schedule = AttendanceRecord::find($id);

        if ($schedule) {
            $schedule->shift_in = $shift_in;
            $schedule->shift_out = $shift_out;

            $schedule->save();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Schedule not found.']);
        }

    }
    public function statisticsAdmin($id)
    {
        $info = [];
        if ($id == null) {
            $id = auth()->user()->id;
        }

        $color = "";
        $total_late_minutes = 0;
        $total_penalty = 0.0;
        $i = 0;
        $c_in = "";
        $c_out = "";
        $fine = 0.0;
        $late_minutes = 0;
        $today = Carbon::today();
        $datetoday = date('Y-m-d', strtotime($today));
        $chj = DB::table('attendance_records')
            ->select('check_in', 'check_out')
            ->where('user_id', $id)
            ->whereDate('shift_in', $datetoday)
            ->first();

        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.week_days as wd',
                'ar.user_id as user_id',
                'ar.shift_in as shift_in',
                'ar.shift_out as shift_out',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff'
            )
            ->where('ar.user_id', $id)
            ->whereMonth('ar.shift_in', Carbon::now()->month)
            ->get();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $approvedLeaves = ApprovedLeave::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('user_id', $id)
            ->get();


        foreach ($data as $record) {
            $start = Carbon::parse($record->shift_in);

            if ($approvedLeaves) {

                foreach ($approvedLeaves as $app) {
                    $date = Carbon::parse($app->date);
                    if ($app->start_time != null && $app->leave_type == "1" && $start->isSameDay($date)) {
                        $chdate = date('Y-m-d', strtotime($app->date));
                        $shift_in = $chdate . $app->start_time;
                        $shift_out = $chdate . $app->end_time;
                        $in = AttendanceRecord::whereDate('shift_in', $chdate)->where('user_id', $id)->first();

                        $up = AttendanceRecord::find($in->id);
                        $up->update([
                            $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_in)),
                            $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out))
                        ]);
                        $color = "";
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
            if ($color == "" && $record->dayoff != "Yes") {

                $currentDate = Carbon::now();
                if ($start->lte($currentDate) && !$record->check_in && !$record->check_out) {
                    $i++;
                    if ($record->wd == "6") {
                        $fine = $fine + 4;

                    } else if ($record->wd == "5") {
                        $fine = $fine + 4.8;
                    }
                }
            }




            if ($record->check_in && $record->check_out && $record->shift_in && $record->shift_out && $color == "" && $record->dayoff != "Yes") {

                $shift_in = Carbon::parse($record->shift_in);
                $shift_out = Carbon::parse($record->shift_out);
                $check_in = Carbon::parse($record->check_in);
                $check_out = Carbon::parse($record->check_out);


                if (
                    (($check_out->format('A') == 'AM' && $shift_out->format('A') == 'PM') ||
                        ($check_out->format('A') == 'PM' && $shift_out->format('A') == 'AM')) &&
                    abs($check_out->diffInHours($shift_out)) > 6
                ) {

                    $i++;
                    if ($record->wd == "6") {
                        $fine = $fine + 4;
                    } else if ($record->wd == "5") {
                        $fine = $fine + 4.8;
                    }
                } else {

                    if ($check_in->greaterThan($shift_in)) {
                        $late_minutes += abs($check_in->diffInMinutes($shift_in));

                        $i++;
                    }

                    if ($check_out->lessThan($shift_out)) {
                        $late_minutes += abs($shift_out->diffInMinutes($check_out));
                        $i++;
                    }

                    // $penalty = ceil($late_minutes / 15) * 0.125;
                    // $total_penalty += $penalty;

                    $total_late_minutes += $late_minutes;

                }

            }
        }
        $week_days = DB::table('users')
            ->select('week_days')
            ->where('id', $id)
            ->first();

        $hour = floor($late_minutes / 60);
        $minute = $late_minutes % 60;


        if ($week_days == "6") {
            if ($total_late_minutes > 0 && $total_late_minutes <= 15) {
                $total_penalty += 0.125;
            } elseif ($late_minutes > 15 && $total_late_minutes <= 540) {
                $range_index = ceil(($total_late_minutes - 15) / 15);
                $total_penalty += 0.125 * $range_index;
            }

        }
        if ($week_days == "5") {
            if ($total_late_minutes > 0 && $total_late_minutes <= 15) {
                $total_penalty += 0.25;
            } elseif ($late_minutes > 15 && $total_late_minutes <= 540) {
                $range_index = ceil(($total_late_minutes - 15) / 15);
                $total_penalty += 0.25 * $range_index;
            }
        }


        $deduction = $total_penalty + $fine;
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
            'day' => $i,
            'hour' => $hour,
            'minute' => $minute,
            'deduction' => $deduction,
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

        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);


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

        $info = [];
        $i = 0;
        $total_late_minutes = 0;
        $total_penalty = 0.0;

        $fine = 0.0;
        $late_minutes = 0;
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
                        $up->update([
                            $up->shift_in = date('Y-m-d H:i:s', strtotime($shift_in)),
                            $up->shift_out = date('Y-m-d H:i:s', strtotime($shift_out))
                        ]);
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

                if (!empty($closestPunchIn)) {
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


            if ($d->check_in && $d->check_out && $d->shift_in && $d->shift_out) {

                $shift_in = Carbon::parse($d->shift_in);
                $shift_out = Carbon::parse($d->shift_out);
                $check_in = Carbon::parse($d->check_in);
                $check_out = Carbon::parse($d->check_out);


                if (
                    (($check_out->format('A') == 'AM' && $shift_out->format('A') == 'PM') ||
                        ($check_out->format('A') == 'PM' && $shift_out->format('A') == 'AM')) &&
                    abs($check_out->diffInHours($shift_out)) > 6
                ) {


                    if (auth()->user()->week_days == "6") {
                        $i++;
                        $fine = $fine + 4;
                    } else if (auth()->user()->week_days == "5") {
                        $fine = $fine + 4.8;
                        $i++;
                    }
                } else {


                    if ($check_in->greaterThan($shift_in)) {
                        $late_minutes += abs($shift_in->diffInMinutes($check_in));
                        $i++;

                    }

                    if ($check_out->lessThan($shift_out)) {
                        $late_minutes += abs($shift_out->diffInMinutes($check_out));
                        $i++;
                    }

                    $total_late_minutes += $late_minutes;

                }

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

                if (!empty($closestPunchOut)) {
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

        $hours = floor($late_minutes / 60);
        $minutes = $late_minutes % 60;




        if (auth()->user()->week_days == "6") {
            if ($late_minutes > 0 && $late_minutes <= 15) {
                $total_penalty += 0.125;
            } elseif ($late_minutes > 15 && $late_minutes <= 540) {
                $range_index = ceil(($late_minutes - 15) / 15);
                $total_penalty += 0.125 * $range_index;
            }

        }
        if (auth()->user()->week_days == "5") {
            if ($late_minutes > 0 && $late_minutes <= 15) {
                $total_penalty += 0.25;
            } elseif ($late_minutes > 15 && $late_minutes <= 540) {
                $range_index = ceil(($late_minutes - 15) / 15);
                $total_penalty += 0.25 * $range_index;
            }
        }


        $deduction = $total_penalty + $fine;
        $in[] = [
            'day' => $i,
            'hour' => $hours,
            'minute' => $minutes,
            'deduction' => $deduction,
            'abc' => $total_penalty
        ];

        return response()->json([

            'data' => $info,
            'in' => $in

        ], 200);
    }
}