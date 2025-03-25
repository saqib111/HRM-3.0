<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UnlockLeaveCategories;
use App\Models\AnnualLeaves;
use App\Models\LeaveManagement;
use App\Models\AssignedLeaveApprovals;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\TelegramService;

class UnlockLeaveController extends Controller
{
    public function view(){
        return view("leave_application.unlock_leave_requests");
    }

    public function applicationsData(Request $request){
        if($request->ajax()){

            $authID = auth()->user()->id;

            $status = $request->input('status', 'pending'); 
    
            $leaves = UnlockLeaveCategories::with('user')->select([
                "id",
                "user_id",
                "title",
                "description",
                "leave_details",
                "leave_balance",
                "images",
                "status"
            ])->where(function ($query) use ($authID, $status) {
                if($status === "pending"){
                    $query->where("status","pending");
                }elseif($status === "approved"){
                    $query->where("status","approved");
                }elseif($status === "rejected"){
                    $query->where("status","rejected");
                }
            })->get();

            
            return Datatables::of($leaves)
            ->addIndexColumn()
                ->addColumn('username', function ($row) {
                    return $row->user->username ?? 'N/A';
                })
                ->addColumn('employee_id', function ($row) {
                    return $row->user->employee_id ?? 'N/A';
                })
                ->addColumn('day', function ($row) {
                    $details = json_decode($row->leave_details);
                    $day_str = '';

                    foreach ($details as $detail) {
                        if ($detail->type === 'full_day') {
                            $day_str .= '<span class="badge bg-primary">Full Day</span><br>';
                        } elseif ($detail->type === 'half_day') {
                            $day_str .= '<span class="badge bg-warning">Half Day</span><br>';
                        }
                    }
                    return $day_str;
                })
                ->addColumn('from', function ($row) {
                    $details = json_decode($row->leave_details);
                    $from_str = '';

                    foreach ($details as $detail) {
                        if ($detail->type === 'full_day') {
                            $from_str .= $detail->start_date . '<br>';
                        } elseif ($detail->type === 'half_day') {
                            $from_str .= $detail->date . ' (' . $detail->start_time . ')<br>';
                        }
                    }
                    return $from_str;
                })
                ->addColumn('to', function ($row) {
                    $details = json_decode($row->leave_details);
                    $to_str = '';

                    foreach ($details as $detail) {
                        if ($detail->type === 'full_day') {
                            $to_str .= $detail->end_date . '<br>';
                        } elseif ($detail->type === 'half_day') {
                            $to_str .= $detail->date . ' (' . $detail->end_time . ')<br>';
                        }
                    }
                    return $to_str;
                })
                ->addColumn('off_days', function ($row) {
                    $details = json_decode($row->leave_details);
                    $off_day_str = '<ul class="list-unstyled mb-0">';

                    foreach ($details as $detail) {
                        if ($detail->type === 'off_day') {
                            $off_day_str .= "<li><span class='badge bg-secondary'>{$detail->date}</span></li>";
                        }
                    }

                    return $off_day_str .= '</ul>';
                })
                ->rawColumns(['day', 'from', 'to', 'off_days'])
                ->make(true);
        }
        return view('leave_application.unlock_leave_request');
    }

    public function applicationDetails($id){
        $leave = UnlockLeaveCategories::with("user")->find($id);
        $group_name = $leave->user->company ? $leave->user->company->name : "No Company";
        
        // Call the helper function to get the username
        $superadmin_id = getUser($leave->superadmin_id);

        $formattedLeaveBalance = rtrim(rtrim($leave->leave_balance, '0'), '.');

        return response()->json([
            'group_name' => $group_name,
            'id' => $leave->id,
            'employee_id' => $leave->user->employee_id,
            'username' => $leave->user->username,
            'title' => $leave->title,
            'description' => $leave->description,
            'leave_details' => json_decode($leave->leave_details), // Ensure leave details are properly decoded
            'leave_balance' => $formattedLeaveBalance,
            'images'=> json_decode($leave->images),
            'status' => $leave->status,
            // Get User ID
            'superadmin_id' => $superadmin_id ?? "Null",
            'superadmin_created_at' => $leave->superadmin_created_at ?? "YYYY-MM-DD HH:MM:SS",
        ]);
    }

    public function unlockLeaveApplication(Request $request){
        // Step 1: Validate the request
        $validator = Validator::make($request->all(), [
            'request_leave_title' => 'required|string|max:255',
            'balance_of_annual_leave' => 'required|numeric',
            'request_leave_category.*' => 'nullable|integer',
            'request_leave_from.*' => 'nullable|date',
            'request_leave_to.*' => 'nullable|date|after_or_equal:request_leave_from.*',
            'images' => 'nullable|array', // Make sure it's an array if files are selected
            'images.*' => 'mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Step 2: Check if the user has already taken a Birthday Leave this year
        $user_id = auth()->user()->id;

        $alreadyAppliedLeave = LeaveManagement::where('user_id', $user_id)
            ->where(function ($query) {
                $query->where('status_1', 'pending')  // Pending in first stage
                    ->orWhere(function ($query) {
                        $query->where('status_1', 'approved')  // Accepted in first stage
                                ->where('status_2', 'pending');  // Pending in second stage
                    });
            })
            ->exists();

        // Check if there's any pending leave application in UnlockLeaveCategories
        $existingUnlockRequest = UnlockLeaveCategories::where('user_id', $user_id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyAppliedLeave || $existingUnlockRequest) {
            // Return a response to inform the user that they already have a pending leave application
            return response()->json(['message' => 'You already have a pending leave application.'], 400);
        }

        $annual_leave_balance = (float) $request->balance_of_annual_leave; // Ensure it's a float to handle fractional part
        $off_days = $request->off_days ?? [];
        $leave_details = [];
        $full_day_leave = $request->request_leave_category;
        $full_leave_from = $request->request_leave_from;
        $full_leave_to = $request->request_leave_to;
        $images = $request->file('images');

        // Process each full-day leave request
        if (!empty($full_day_leave)) {
            foreach ($full_day_leave as $index => $leave_type) {
                $from = $full_leave_from[$index];
                $to = $full_leave_to[$index];

                // Cast leave_type to integer
                $leave_type = (int) $leave_type;

                // Calculate the effective leave days excluding off-days
                $effective_days = $this->calculateLeaveDays($from, $to, $off_days);

                if ($leave_type == 1) { // Annual Leave
                    // Separate the integer and fractional parts of the balance
                    $whole_days_balance = floor($annual_leave_balance);
                    $fractional_balance = $annual_leave_balance - $whole_days_balance;

                    if ($effective_days > $whole_days_balance) {
                        // Calculate the maximum date up to which annual leave can be applied
                        $paid_end_date = $this->calculatePaidEndDate($from, $whole_days_balance, $off_days);

                        // Apply annual leave for the whole days only
                        if ($whole_days_balance > 0) {
                            $leave_details[] = [
                                'type' => 'full_day',
                                'leave_type_id' => 1,
                                'start_date' => $from,
                                'end_date' => $paid_end_date,
                                'status' => 'paid'
                            ];
                        }

                        // Remaining days after paid leave are converted to unpaid leave
                        $unpaid_start_date = (new \DateTime($paid_end_date))->modify('+1 day')->format('Y-m-d');
                        $leave_details[] = [
                            'type' => 'full_day',
                            'leave_type_id' => 4, // Unpaid Leave
                            'start_date' => $unpaid_start_date,
                            'end_date' => $to,
                            'status' => 'unpaid'
                        ];

                        // Reset the annual leave balance to the fractional part only (e.g., 0.5 day)
                        $annual_leave_balance = $fractional_balance;
                    } else {
                        // Entire period fits within Annual Leave balance (whole days + fractional part if applicable)
                        $leave_details[] = [
                            'type' => 'full_day',
                            'leave_type_id' => 1,
                            'start_date' => $from,
                            'end_date' => $to,
                            'status' => 'paid'
                        ];

                        // Deduct from Annual Leave balance
                        $annual_leave_balance -= $effective_days;
                    }
                } else {
                    // For other leave types, store as paid leave
                    $leave_details[] = [
                        'type' => 'full_day',
                        'leave_type_id' => $leave_type,
                        'start_date' => $from,
                        'end_date' => $to,
                        'status' => 'paid'
                    ];
                }
            }
        }

        // Process half-day leaves if present
        if (!empty($half_day_date)) {
            foreach ($half_day_date as $index => $half_day_date) {
                $start_time = $half_day_start_time[$index];
                $end_time = $half_day_end_time[$index];

                $leave_details[] = [
                    'type' => 'half_day',
                    'leave_type_id' => 1, // Assuming Annual Leave covers half-day leave
                    'date' => $half_day_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ];

                // Deduct 0.5 day from balance if there's any fractional part
                $annual_leave_balance -= 0.5;
            }
        }

        // Process off-days
        if (!empty($request->off_days)) {
            foreach ($request->off_days as $off_day) {
                $leave_details[] = [
                    'type' => 'off_day',
                    'date' => $off_day
                ];
            }
        }

        // IMAGES WORK
        $imagePaths = [];
    
        // If there are images, handle the upload
        if ($images) {
            foreach ($images as $image) {
                // Generate a unique file name for each image
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Define the destination path within the public folder
                $destinationPath = public_path('other_leave_images');
                
                // Ensure the folder exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true); // Create the directory if it doesn't exist
                }
    
                // Store the image in the 'public/other_leave_images' folder
                $image->move($destinationPath, $imageName);
                
                // Store the path of the image in the array
                $imagePaths[] = 'other_leave_images/' . $imageName;
            }
        }
        
        // Step 6: Save leave application to the database
        $leave = UnlockLeaveCategories::create([
            'user_id' => $user_id,
            'title' => $request->request_leave_title,
            'description' => $request->request_leave_description,
            'leave_balance' => $request->balance_of_annual_leave,
            'leave_details' => json_encode($leave_details),
            "images" => json_encode($imagePaths),
            'status' => 'pending',
            'superadmin_created_at' => now(),
        ]);

        $leaveTypeMapping = [
            1 => 'Annual Leave',
            2 => 'Sick Leave',
            3 => 'Marriage Leave',
            4 => 'Unpaid Leave',
            5 => 'Hospitalization Leave',
            6 => 'Compassionate Leave',
            7 => 'Maternity Leave',
            8 => 'Paternity Leave',
            // Add any other leave types here
        ];

        $leaveTypes = [];
        foreach ($request->request_leave_category as $leaveType) {
            // Check if the leave type exists in the mapping, otherwise default to 'Unknown Leave'
            $leaveTypes[] = $leaveTypeMapping[$leaveType] ?? 'Unknown Leave';
        }
         
        
        $user = auth()->user();
        // TELEGRAM BOT NOTIFICATION BOT
        $message = "New leave application requested. \n\n";
        $message .= "Username: " . $user->username . "\n\n";  
        $message .= "Employee ID: " . $user->employee_id . "\n\n";  
        $message .= "Title: " . $request->request_leave_title . "\n\n";
        $message .= "Leave Type: " . implode(', ', $leaveTypes) . "\n\n";
        $message .= "From: " . $request->request_leave_from[0] . "\n\n";
        $message .= "To: " . $request->request_leave_to[0] . "\n\n";

        $this->telegramService->sendTelegramMessage($message);


        return response()->json(['message' => 'Leave application submitted successfully.'], 200);
    }

    // HELPER FUNCTION TO CALCULATE
    // Helper to calculate leave days excluding off-days
    private function calculateLeaveDays($start, $end, $off_days = [])
    {
        $start_date = new \DateTime($start);
        $end_date = new \DateTime($end);
        $days_count = 0;

        while ($start_date <= $end_date) {
            $current_date_str = $start_date->format('Y-m-d');

            if (!in_array($current_date_str, $off_days)) {
                $days_count++;
            }

            $start_date->modify('+1 day');
        }

        return $days_count;
    }

    // Helper to calculate the exact end date for Annual Leave based on balance (whole days only)
    private function calculatePaidEndDate($start, $annual_leave_balance, $off_days = [])
    {
        $current_date = new \DateTime($start);
        $days_used = 0;

        while ($days_used < $annual_leave_balance) {
            $current_date_str = $current_date->format('Y-m-d');

            if (!in_array($current_date_str, $off_days)) {
                $days_used++;
            }

            if ($days_used < $annual_leave_balance) {
                $current_date->modify('+1 day');
            }
        }

        return $current_date->format('Y-m-d');
    }

    // UNLOCK LEAVE REQUEST ACTION
    public function unlockLeaveAction(Request $request){
        $request->validate([
            'leave_id' => 'required|integer',
            'leave_action' => 'required|string',
        ]);

        $leave_id = $request->leave_id;
        $leave_action = $request->leave_action;
        $active_user_id = auth()->user()->id;
        
        if($leave_action === "approve_request"){

            $leaveUpdate = UnlockLeaveCategories::join('assigned_leave_approvals', 'unlock_leave_categories.user_id', '=', 'assigned_leave_approvals.user_id')
                ->where('unlock_leave_categories.id', $leave_id)
                ->select(
                    'unlock_leave_categories.*', // Select all columns from UnlockLeaveCategories
                    'assigned_leave_approvals.first_assign_user_id',
                    'assigned_leave_approvals.second_assign_user_id'
                )
                ->first();
            $leaveUpdate->status = "approved";
            $leaveUpdate->superadmin_id = $active_user_id;
            $leaveUpdate->superadmin_created_at = now();

            $leaveUpdate->save();

            $tester = "LEAVE REQUEST ACCEPTED";

            $add_leave = LeaveManagement::create([
                'user_id' => $leaveUpdate->user_id,
                'title' => $leaveUpdate->title,
                'description' => $leaveUpdate->description,
                'leave_balance' => $leaveUpdate->leave_balance,
                'team_leader_ids' => $leaveUpdate->first_assign_user_id,
                'manager_ids' => $leaveUpdate->second_assign_user_id,
                'leave_details' => $leaveUpdate->leave_details,
            ]);
        
        }elseif($leave_action === "reject_request"){

            $leaveUpdate = UnlockLeaveCategories::find($leave_id);
            $leaveUpdate->status = "rejected";
            $leaveUpdate->superadmin_id = $active_user_id;
            $leaveUpdate->superadmin_created_at = now();

            $leaveUpdate->save();

            $tester = "LEAVE REQUEST DENIED";
        
        }
        return response()->json([
            'success' => true,
            'leave_id' => $leave_id,
            'leave_action' => $leave_action,
            'tester' => $tester
        ]);
    }

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    // public function checkPendingLeave()
    // {
    //     $user_id = auth()->user()->id;
        
    //     // Check if there is a leave application with pending status
    //     $pendingLeave = UnlockLeaveCategories::where('user_id', $user_id)->where('status', 'pending')->first();
    //     $pendingLeave = UnlockLeaveCategories::join('leave_management', 'unlock_leave_categories.user_id', '=', 'leave_management.user_id')
    //     ->where(function ($query) {
    //         $query->where('unlock_leave_categories.status', 'pending')
    //             ->orWhere(function ($query) {
    //                 $query->where('leave_management.status_1', 'pending')
    //                         ->orWhere('leave_management.status_2', 'pending');
    //             });
    //     })
    //     ->first();
        
    //     if ($pendingLeave) {
    //         // Decode the leave_details JSON field properly
    //         $leaveDetails = json_decode($pendingLeave->leave_details, true); // Decode as associative array

    //         if (isset($leaveDetails[0])) {
    //             // Assuming the first item in the decoded array is the leave detail
    //             $leaveDetail = $leaveDetails[0];
    //             return response()->json([
    //                 'has_pending_leave' => true,
    //                 'leave_details' => [
    //                     'title' => $pendingLeave->title,
    //                     'category' => $leaveDetail['leave_type_id'], // Assuming the 'type' is the leave category
    //                     'start_date' => $leaveDetail['start_date'],
    //                     'end_date' => $leaveDetail['end_date'],
    //                     'status' => $pendingLeave->status
    //                 ]
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'has_pending_leave' => true,
    //                 'message' => 'Leave details are missing or corrupted'
    //             ], 400); // If the leave details are not as expected
    //         }
    //     } else {
    //         // If there is no pending leave, return false
    //         return response()->json(['has_pending_leave' => false]);
    //     }
    // }


}
