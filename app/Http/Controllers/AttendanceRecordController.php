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
    FingerPrint
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


        $info = [];
        $id = auth()->user()->id;

        $i = 0;
        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'ar.user_id as user_id',
                'u.employee_id as emp_id',
                'ar.shift_in as shift_in',
                'ar.id as id',
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
            $i++;
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

                    if ($min_dif1 <= 10) {
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
                    if ($min_dif2 <= 10) {
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
            $shift_in = date('H:i:s A', strtotime($d->shift_in));
            $end_date = date('d F Y', strtotime($d->shift_out));
            $shift_out = date('H:i:s A', strtotime($d->shift_out));
            if ($d->check_in != null) {
                $check_in = date('H:i:s A', strtotime($d->check_in));
            } else {
                $check_in = $d->check_in;
            }
            if ($d->check_out != null) {
                $check_out = date('H:i:s A', strtotime($d->check_out));
            } else {
                $check_out = $d->check_out;
            }

            if ($d->check_in != null && $d->check_out != null) {
                $cin = date('Y-m-d H:i:s', strtotime($d->check_in));
                $cout = date('Y-m-d H:i:s', strtotime($d->check_out));


                $sh_in = Carbon::parse($cin);
                $sh_out = Carbon::parse($cout);
                $difference = $sh_in->diff($sh_out);
                if ($difference->h >= 9 && $difference->h < 15) {
                    $duty_hours = "9:00:00";
                } elseif ($difference->h < 9) {
                    $duty_hours = $difference->h . ":" . $difference->i . ":" . $difference->s;
                } elseif ($difference->h > 15) {
                    $duty_hours = "00:00:00";
                }

            } else {
                $duty_hours = "00:00:00";
            }

            $dayoff = $d->dayoff;


            array_push($info, [
                'no' => $i,
                'start_date' => $start_date,
                'shift_in' => $shift_in,
                'end_date' => $end_date,
                'shift_out' => $shift_out,
                'dayoff' => $dayoff,
                'check_in' => $check_in,
                'check_out' => $check_out,
                'duty_hours' => $duty_hours,
                'verify1' => $v1,
                'verify2' => $v2
            ]);
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

        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'ar.user_id as user_id',
                'u.employee_id as emp_id',
                'ar.shift_in as shift_in',
                'ar.id as id',
                'ar.shift_out as shift_out',
                'ar.duty_hours as duty_hours',
                'ar.check_in as check_in',
                'ar.check_out as check_out',
                'ar.dayoff as dayoff'
            )
            ->where('ar.user_id', $id)
            ->whereBetween('ar.shift_in', [$from, $to])
            ->get();

        foreach ($data as $d) {
            $i++;
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

                    if ($min_dif1 <= 10) {
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
                    if ($min_dif2 <= 10) {
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
            $shift_in = date('H:i:s A', strtotime($d->shift_in));
            $end_date = date('d F Y', strtotime($d->shift_out));
            $shift_out = date('H:i:s A', strtotime($d->shift_out));
            if ($d->check_in != null) {
                $check_in = date('H:i:s A', strtotime($d->check_in));
            } else {
                $check_in = $d->check_in;
            }
            if ($d->check_out != null) {
                $check_out = date('H:i:s A', strtotime($d->check_out));
            } else {
                $check_out = $d->check_out;
            }
            if ($d->check_in != null && $d->check_out != null) {
                $cin = date('Y-m-d H:i:s', strtotime($d->check_in));
                $cout = date('Y-m-d H:i:s', strtotime($d->check_out));


                $sh_in = Carbon::parse($cin);
                $sh_out = Carbon::parse($cout);
                $difference = $sh_in->diff($sh_out);
                if ($difference->h >= 9 && $difference->h < 15) {
                    $duty_hours = "9:00:00";
                } elseif ($difference->h < 9) {
                    $duty_hours = $difference->h . ":" . $difference->i . ":" . $difference->s;
                } elseif ($difference->h > 15) {
                    $duty_hours = "00:00:00";
                }

            } else {
                $duty_hours = "00:00:00";
            }

            $dayoff = $d->dayoff;


            array_push($info, [
                'no' => $i,
                'start_date' => $start_date,
                'shift_in' => $shift_in,
                'end_date' => $end_date,
                'shift_out' => $shift_out,
                'dayoff' => $dayoff,
                'check_in' => $check_in,
                'check_out' => $check_out,
                'duty_hours' => $duty_hours,
                'verify1' => $v1,
                'verify2' => $v2
            ]);
        }

        return response()->json([

            'data' => $info,
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
        $punch = AttendanceSession::where('user_id', $user)->where('check_out', NULL)->latest()->first();
        if ($punch) {
            $shift_in = date('Y-m-d H:i', strtotime($punch->shift_in));
            $shift_end = date('Y-m-d H:i', strtotime($punch->shift_out));
            $start = new Carbon($shift_in);
            $end = new Carbon($shift_end);
            $duration = $start->diff($end);


            $hoursWorked = Carbon::parse($punch->check_in)->diffInHours(Carbon::now());
            return response()->json([
                'punch_in_time' => $punch->check_in,
                'shift_duration' => $duration->h
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

    public function statistics()
    {
        $checkInTime = date('Y-m-d');
        $info = [];
        $id = auth()->user()->id;
        $i = 0;
        $deduction = 0;
        $tot_hour = 0;
        $tot_minute = 0;
        $data = DB::table('attendance_records as ar')
            ->join('users as u', 'u.id', '=', 'ar.user_id')
            ->select(
                'u.username as name',
                'ar.user_id as user_id',
                'u.employee_id as emp_id',
                'u.week_days as week_day',
                'ar.shift_in as shift_in',
                'ar.id as id',
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
            $check_in = $d->check_in;
            $shift_in = $d->shift_in;
            $check_out = $d->check_out;
            $shift_out = $d->shift_out;
            $week_day = $d->week_day;
            if ($check_in != null && $check_out != null && $shift_in != null && $shift_out != null) {
                if ($shift_in < $check_in || $shift_out > $check_out) {

                    $i++;
                    $sh_in = Carbon::parse($shift_in);
                    $sh_out = Carbon::parse($shift_out);
                    $ch_in = Carbon::parse($check_in);
                    $ch_out = Carbon::parse($check_out);

                    $sh_dif = $sh_in->diffInMinutes($sh_out);
                    $ch_diff = $ch_in->diffInMinutes($ch_out);

                    $final_difference = $sh_dif - $ch_diff;


                    $hours = intdiv($final_difference, 60);
                    $minutes = abs($final_difference % 60);
                    $tot_hour = $tot_hour + $hours;
                    $tot_minute = $tot_minute + $minutes;

                    if ($week_day == "6") {
                        if ($final_difference <= 15) {
                            $deduction += 0.125;
                        } elseif ($final_difference <= 540) {
                            $range_index = ceil(($final_difference - 15) / 15);
                            $deduction += 0.125 * $range_index;
                        } elseif ($final_difference > 900) {
                            $deduction += 4;
                        }

                    }
                    if ($week_day == "5") {
                        if ($final_difference <= 15) {
                            $deduction += 0.25;
                        } elseif ($final_difference <= 540) {
                            $range_index = ceil(($final_difference - 15) / 15);
                            $deduction += 0.25 * $range_index;
                        } elseif ($final_difference > 900) {
                            $deduction += 4.5;
                        }
                    }


                }
            }
        }
        if ($tot_minute >= 60) {
            $hour = intdiv($tot_minute, 60);
            $tot_hour = $tot_hour + $hour;
            $minute = $tot_minute % 60;
            $tot_minute = $minute;

        }
        $check = DB::table('attendance_records')
            ->select('check_in', 'check_out')
            ->where('user_id', $id)
            ->whereDate('shift_in', $checkInTime)
            ->first();

        if ($check->check_in != null) {
            $check_in = date('H:i A', strtotime($check->check_in));
        } else {
            $check_in = "";
        }
        if ($check->check_out != null) {
            $check_out = date('H:i A', strtotime($check->check_out));
        } else {
            $check_out = "";
        }


        array_push($info, ['days' => $i, 'hours' => $tot_hour, 'minute' => $tot_minute, 'deduction' => $deduction, 'check_in' => $check_in, 'check_out' => $check_out]);
        return response()->json([
            'status' => 'success',
            'info' => $info

        ]);
    }

    public function checkEmpAuhentication(Request $request)
    {

        $userData = array(
            'email' => $request->email,
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

}
