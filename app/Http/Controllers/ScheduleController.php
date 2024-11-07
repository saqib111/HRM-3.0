<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\{
    DB,
    Session,
    Http
};
use App\Models\{

    User,
    LeaderEmployee,
    AttendanceRecord,
    Group,
    AttendanceSession,
    FingerPrint,
};

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function dataTable(Request $request)
    {
        $uid = auth()->user()->id;
        $schedule = Schedule::where('user_id', $uid)->where('status', '0');


        if ($request->has('name')) {
            $schedule->where('name', $request->input('shift_name'));
        }

        return DataTables::of($schedule)
            ->addIndexColumn()
            // Make company_name searchable using the "whereHas" filter for joined fields


            ->filterColumn('Shift Name', function ($query, $keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%");

            })

            ->addColumn('action', function ($row) {
                $btn = '<button class="btn btn-danger" onclick="deleSchedule(' . $row->id . ')"><i class="fa fa-trash"></i></button>';
                $btn = '<button class="btn btn-danger" onclick="editSchedule(' . $row->id . ')"><i class="fa fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    public function index()
    {


        return view('schedule.schedule');
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

        $id = auth()->user()->id;
        $start = $request->start_date;

        $end = $request->end_date;
        $starray = (explode(" , ", $start));

        $start_dates = preg_split("/\,/", $starray[0]);

        $start_end = last($start_dates);
        $endarray = (explode(" , ", $end));
        $end_date = preg_split("/\,/", $endarray[0]);
        $end_end = last($end_date);

        $schedule = new Schedule();
        $schedule->start_to = date('Y-m-d', strtotime($start_dates[0]));
        $schedule->start_end = date('Y-m-d', strtotime($start_end));
        $schedule->start_time = $request->start_time;
        $schedule->end_to = date('Y-m-d', strtotime($end_date[0]));
        $schedule->end_end = date('Y-m-d', strtotime($end_end));
        $schedule->end_time = $request->end_time;
        $schedule->name = $request->shift_name;
        $schedule->user_id = $id;
        $schedule->save();
        if ($schedule) {
            $dbAll = DB::table('leader_employees')
                ->select('id')
                ->where('Leader_id', $id)
                ->where('status', '1')
                ->get();

            if ($dbAll) {
                foreach ($dbAll as $db) {
                    $check = LeaderEmployee::find($db->id);
                    $check->update([
                        $check->status = '0',
                    ]);
                }
            }
            $group = DB::table('groups')
                ->select('id')
                ->where('leader_id', $id)
                ->where('status', '0')
                ->get();
            if ($group) {
                foreach ($group as $db) {
                    $check = Group::find($db->id);
                    $check->update([
                        $check->status = '1',
                    ]);
                }
            }

        }

        return response()->json([


            'message' => 'Schedule created successfully!'
        ], 200);



    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
    public function assignEmployee()
    {


        return view('schedule.assign-employee');
    }
    public function findData($id)
    {
        $schedule = Schedule::where('id', $id)->get();
        $uid = auth()->user()->id;
        $group = Group::where('leader_id', $uid)->where('status', '1')->get();

        return response()->json(["schedule" => $schedule, 'group' => $group]);
    }
    public function attendanceRecord(Request $request)
    {
        $leaderid = auth()->user()->id;
        $startDate = date('Y-m-d', strtotime($request->start_to));
        $endDate = date('Y-m-d', strtotime($request->start_end));
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $start_date = array_map(fn($date) => $date->format('Y-m-d'), iterator_to_array($dateRange));

        $endStart = date('Y-m-d', strtotime($request->end_to));
        $endEnd = date('Y-m-d', strtotime($request->end_end));
        $dateRange2 = CarbonPeriod::create($endStart, $endEnd);
        $end_date = array_map(fn($date) => $date->format('Y-m-d'), iterator_to_array($dateRange2));

        if (!empty($request->group_id)) {
            $group = DB::table('groups')
                ->select('user_id')
                ->where('id', $request->group_id)
                ->get();


            $user_id = explode(',', $group[0]->user_id);

            for ($j = 0; $j < sizeof($user_id); $j++) {
                for ($i = 0; $i < sizeof($start_date); $i++) {
                    $shift_start = $start_date[$i] . $request->start_time;
                    $shift_in = date('Y-m-d H:i', strtotime($shift_start));
                    $shift_end = $end_date[$i] . $request->end_time;
                    $shift_out = date('Y-m-d H:i', strtotime($shift_end));

                    $start = new Carbon($shift_in);
                    $end = new Carbon($shift_out);
                    $duration = $start->diff($end);

                    $timeTable = new AttendanceRecord();
                    $timeTable->leader_id = (int) $leaderid;
                    $timeTable->user_id = (int) $user_id[$j];
                    $timeTable->shift_id = $request->schedule_id;
                    $timeTable->shift_in = $shift_in;
                    $timeTable->shift_out = $shift_out;
                    $timeTable->duty_hours = $duration->h;
                    $timeTable->status = "1";
                    $timeTable->dayoff = "No";
                    $timeTable->save();

                }
                $groupUpdate = Group::find($request->group_id);
                $groupUpdate->update([
                    $groupUpdate->status = "0"
                ]);
            }

            $check = Schedule::find($request->schedule_id);

            if (!empty($check)) {
                $check->update([
                    $check->status = "1",
                ]);
            }


        }


        return response()->json([

            'success' => true,
            'message' => 'Employee Schedule made successfully!',
        ], 200);

    }

    //  public function editEmployeeSchedule()
//  {
//     $uid=auth()->user()->id;
//     $records=LeaderEmployee::select('employee_id')->where('Leader_id',$uid)->get();
//     $info=scheduleInfo($uid);
//     $date=$info[0][0];
//     $scheduleDate=$date->date;

    //     $schedule=$info[1];
//     $employeeInfo=[];
//   if(!empty($records)){
//     foreach($records as $record)
//     {
//         foreach($schedule as $sch)
//         {
//             $checkInfo=DB::table('attendance_records')
//                 ->select('shift_id','user_id')
//                 ->where('user_id',$record->employee_id)
//                 ->where('shift_id',$sch->id)
//                 ->where('check_in','=',NULL)
//                 ->latest('shift_id')
//                 ->first();
//               if($checkInfo!=null)
//               {
//                 array_push($employeeInfo, $checkInfo); 
//               }  
//         } 

    //     }
//   }


    //     $shift_all=[];
//     if(!empty($employeeInfo))
//     {
//         for($i=0;$i<sizeof($employeeInfo);$i++)
//     {
//         $shift=DB::table('schedules')
//                ->select('*')
//                ->where('id',$employeeInfo[$i]->shift_id)
//                ->get();

    //         $username=User::select('id','username')->where('id',$employeeInfo[$i]->user_id) ->get();
//         $data=[$shift,$username];  
//         array_push($shift_all, $data) ;    
//     }
//     }

    //     return view('schedule.edit-employee-schedule',['shiftAll'=>$shift_all]);
//  }  

    //  public function scheduleDtattable()
//  {

    //     return DataTables::of($shift_all);
//  }

    public function manageSchedule()
    {

        return view('schedule.manage-schedule');


    }
    public function manageScheduleData(Request $request)
    {
        $uid = auth()->user()->id;

        $schedule = Schedule::where('user_id', $uid)->where('status', '1');
        if ($request->ajax()) {

            if ($request->has('name')) {
                $schedule->where('name', $request->input('shift_name'));
            }

            return DataTables::of($schedule)
                ->addIndexColumn()

                ->filterColumn('Shift Name', function ($query, $keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%");

                })

                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-danger" onclick="deleSchedule(' . $row->id . ')"><i class="fa fa-trash"></i></button>';
                    $btn = '<button class="btn btn-danger" onclick="editSchedule(' . $row->id . ')"><i class="fa fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

    }
    public function updateSchedule(Request $request)
    {
        $check = Schedule::find($request->id);
        $check->update([
            $check->status = '0',
        ]);
        $id = '0';
        return response()->json([
            'id' => $id,

            'message' => 'Schedule status updated successfully!'
        ], 200);
    }



    public function deleteSchedule($id)
    {
        $check = Schedule::find($id);
        if ($check) {
            $check->delete();

            return response()->json([
                'message' => 'Schedule status updated successfully!'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something wrong!'
            ], 300);

        }

    }

    public function addHoliday()
    {
        return view('schedule.holiday');
    }
    public function getEmolyee()
    {
        $names = [];
        $id = [];
        $lid = auth()->user()->id;
        $employee = DB::table('leader_employees as lu')
            ->join('users as u', 'u.id', '=', 'lu.employee_id')
            ->select('u.username as name', 'u.id as id')
            ->where('lu.Leader_id', $lid)
            ->where('lu.off_day', NULL)
            ->get();

        return ($employee);
    }
    public function submitHoliday(Request $request)
    {
        $uid = auth()->user()->id;
        $user_id = $request->employee_id;
        $date = $request->date;
        $dateArray = (explode(" , ", $date));
        $dates = preg_split("/\,/", $dateArray[0]);
        $month = date('m', strtotime($dates[0]));
        foreach ($user_id as $user) {
            $check = DB::table('attendance_records')
                ->select('*')
                ->where('user_id', $user)
                ->whereMonth('shift_in', '=', $month)
                ->latest()
                ->get();
            if (count($check) > 0) {
                foreach ($check as $ck) {
                    $fresh = attendanceRecord::find($ck->id);
                    $fresh->update([
                        $fresh->dayoff = "No"
                    ]);

                    foreach ($dates as $dt) {
                        if (date('Y-m-d', strtotime($dt)) == date('Y-m-d', strtotime($ck->shift_in))) {
                            $updateOff = attendanceRecord::find($ck->id);
                            $updateOff->update([
                                $updateOff->dayoff = "Yes"
                            ]);

                        }

                    }

                }

            } else {
                return response()->json([

                ], 300);
            }

        }
    }

    public function groupNameData(Request $request)
    {

        $uid = auth()->user()->id;
        $userId = [];
        $info = [];
        $check = DB::table('groups')
            ->select('*')
            ->orderBy('id', 'desc')
            ->where('leader_id', $uid)
            ->get();

        foreach ($check as $ch) {
            $user = explode(',', $ch->user_id);
            foreach ($user as $us) {
                $name = DB::table('users')
                    ->select('username', 'id')
                    ->where('id', $us)
                    ->first();
                array_push($info, ['group_name' => $ch->name, 'group_id' => $ch->id, 'name' => $name->username, 'id' => $name->id]);

            }

        }


        return response()->json([

            'groups' => $info,
        ], 200);


    }

    public function test(Request $request, $id)
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
                if ($difference->h >= 9) {
                    $duty_hours = "9:00:00";
                } else {
                    $duty_hours = $difference->h . ":" . $difference->i . ":" . $difference->s;
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
        dd($info);
        return response()->json([

            'data' => $info,
        ], 200);


    }
}