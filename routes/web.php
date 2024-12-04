<?php


use App\Http\Middleware\CheckUserProfilePermission;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\{

    AdminController,
    ProfileController,
    UserProfileController,
    CompanyController,
    ScheduleController,
    GroupController,
    BrandController,
    DepartmentController,
    DesignationController,
    LeaveController,
    RolesPermissionsController,
    AllEmployeesController,
    AssignedLeaveApprovalsController,
    AnnualLeavesController,
    ExpiredVisaInfoController,
    AttendanceRecordController,
    LeaderEmployeeController,
    PayrollController,
    FingerprintController,
    SettingController
};

Route::get('/', function () {
    return view('index');
});
Route::get('/login', function () {
    return view('index');
});
Route::post('loginto', [AuthenticatedSessionController::class, 'store'])->name('loginto');
// Route::get('admin-dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [AdminController::class, 'showEmployee'])->name('employees');
    Route::get('/employees-list', [AdminController::class, 'showEmployeeList'])->name('employees-list');
    // Route::post('/add-employee', [AdminController::class, 'addEmployee'])->name('add.employee');
    // Route::get('/manage-employees', [AdminController::class, 'getEmployee'])->name('list.employee');
    Route::get('/get-employee/{id}', [AdminController::class, 'editEmployee'])->name('edit.employee');
    Route::post('/update-employee', [AdminController::class, 'updateEmployee'])->name('update.employee');
    Route::delete('/delete-employee/{id}', [AdminController::class, 'deleteEmployee'])->name('delete.employee');
    Route::get('/check-designation/{id}', [AdminController::class, 'checkDesignation'])->name('check.designation');

    // Routes for managing employees
    Route::middleware(['auth', 'check_permission'])->group(function () {

        Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard')
            ->defaults('permission', 'dashboard');

        Route::post('/add-employee', [AdminController::class, 'addEmployee'])
            ->name('add.employee')
            ->defaults('permission', 'create_user');

        Route::get('/manage-employees', [AdminController::class, 'getEmployee'])
            ->name('list.employee')
            ->defaults('permission', 'show_users'); // Set 'permission' in the route defaults

        // Show "Manage Team" Table --------------------------------------------------------
        Route::get('create-team', [AdminController::class, 'createTeam'])
            ->name('create.team')
            ->defaults('permission', 'show_teams');

        Route::post('team-store', [LeaderEmployeeController::class, 'store'])
            ->name('team.store')
            ->defaults('permission', 'create_team');

        Route::get('team-delete/{id}', [LeaderEmployeeController::class, 'teamDelete'])
            ->name('delete.team')
            ->defaults('permission', 'delete_team');

        // All Employee Info Routes

        Route::get('all-employees', [UserProfileController::class, 'allEmployee'])
            ->name('all.employees')
            ->defaults('permission', 'show_all_employee_info');

        // AL Balance Routes
        Route::prefix('annual-leaves')->name('annual-leaves.')->group(function () {
            Route::get('/', [AnnualLeavesController::class, 'index'])
                ->name('index')
                ->defaults('permission', 'show_al_balance');

            Route::get('{annualLeave}/edit', [AnnualLeavesController::class, 'edit'])
                ->name('edit')
                ->defaults('permission', 'update_al_balance');

            Route::put('{annualLeave}', [AnnualLeavesController::class, 'update'])
                ->name('update')
                ->defaults('permission', 'update_al_balance');

        });

        // Fingerprint Record Routes
        // Route::resource('fingerprint-record', FingerprintController::class);
        Route::prefix('fingerprint-record')->name('fingerprint-record.')->group(function () {
            Route::get('/', [FingerprintController::class, 'index'])
                ->name('index')
                ->defaults('permission', 'show_fingerprint_record');

            Route::get('{fingerprint-record}', [FingerprintController::class, 'show'])
                ->name('show')
                ->defaults('permission', 'show_fingerprint_record');

            Route::get('{fingerprint-record}/edit', [FingerprintController::class, 'edit'])
                ->name('edit')
                ->defaults('permission', 'update_fingerprint_status');

            Route::put('{fingerprint-record}', [FingerprintController::class, 'update'])
                ->name('update')
                ->defaults('permission', 'update_fingerprint_status');

            Route::delete('{fingerprint-record}', [FingerprintController::class, 'destroy'])
                ->name('destroy')
                ->defaults('permission', 'delete_fingerprint_record');
        });
        Route::get('/search-users', [FingerprintController::class, 'searchUsers'])
            ->name('search.users')
            ->defaults('permission', 'show_fingerprint_record');

    });

    // ROUTES FOR USER PROFILE
    Route::middleware(['auth', 'check_user_permission'])->group(function () {
        Route::get('/view-user-profile/{id}', [UserProfileController::class, 'profileShow'])->name('user-profile.customDetails')->defaults('permission', 'update_employee_info');
        Route::post('/updateVisaInfo', [UserProfileController::class, 'updateVisaInfo'])->name('update.visainfo')->defaults('permission', 'update_employee_info');
        Route::post('/emergency-update', [UserProfileController::class, 'updateEmergency'])->name('emergency.update')->defaults('permission', 'update_employee_info');
        Route::post('/dependant-update', [UserProfileController::class, 'updateDependant'])->name('dependant.update')->defaults('permission', 'update_employee_info');
    });


    //Schedule Start
    Route::get('schedule-list', [ScheduleController::class, 'index'])->name('schedule');
    Route::resource('schedule', ScheduleController::class);
    Route::get('/schedule-data', [ScheduleController::class, 'dataTable'])->name('schedule.data');
    Route::get('/schedule-data-search/{id}', [ScheduleController::class, 'findData'])->name('schedule.get');
    Route::get('/assign-employee', [ScheduleController::class, 'assignEmployee'])->name('assign.employee');
    Route::post('/attendance-record', [ScheduleController::class, 'attendanceRecord'])->name('attendancerecord.store');
    Route::get('/manage-schedule', [ScheduleController::class, 'manageSchedule'])->name('schedule.manage');
    Route::get('/manage-schedule-data', [ScheduleController::class, 'manageScheduleData'])->name('manage.scheduledata');
    Route::post('/manage-schedule-data', [ScheduleController::class, 'updateSchedule'])->name('schedule.update');
    Route::get('/delete-schedule/{id}', [ScheduleController::class, 'deleteSchedule'])->name('schedule.delete');
    Route::get('/add-holiday', [ScheduleController::class, 'addHoliday'])->name('add.holiday');
    Route::post('/holiday', [ScheduleController::class, 'submitHoliday'])->name('holiday.submit');
    Route::resource('group', GroupController::class);
    Route::get('group-data', [GroupController::class, 'groupData'])->name('group.data');
    Route::get('group-employee', [GroupController::class, 'groupEmployee'])->name('group.employee');
    Route::get('change-employee-group', [GroupController::class, 'groupChange'])->name('group.change');
    Route::post('change-group', [GroupController::class, 'changeGroupData'])->name('changegroup.data');
    Route::get('group-member/{id}', [GroupController::class, 'groupMember'])->name('group.member');
    Route::get('get-employee', [ScheduleController::class, 'getEmolyee'])->name('employee.get');
    Route::get('manage-attendance', [AttendanceRecordController::class, 'index'])->name('attendance.manage');
    Route::get('attendance-record', [AttendanceRecordController::class, 'attendanceRecord'])->name('attendance.record');
    Route::get('groupuser-data', [ScheduleController::class, 'groupNameData'])->name('groupname.data');
    // Schedule Ends

    // Schedule Ends
    Route::get('leader', [LeaderEmployeeController::class, 'leader'])->name('leader');
    Route::get('team-data', [LeaderEmployeeController::class, 'teamData'])->name('team.data');

    //Attendance---------
    Route::get('attendance-employee', [AttendanceRecordController::class, 'attendanceEmployeeRecord'])->name('attendanceemployee.record');
    Route::get('attendance-employee-record', [AttendanceRecordController::class, 'attendanceRecord'])->name('attendance.detail');
    Route::post('search-record', [AttendanceRecordController::class, 'searchRecord'])->name('search.record');
    Route::post('attendance-checkin', [AttendanceRecordController::class, 'attendanceCheckIn'])->name('attendance.checkin');
    Route::post('/punch-in', [AttendanceRecordController::class, 'punchIn']);
    Route::post('/check-employee-authentication', [AttendanceRecordController::class, 'checkEmpAuhentication'])->name('check.emp');
    Route::get('/get-punch-time', [AttendanceRecordController::class, 'checkStatus'])->name('check.attendance');
    Route::post('/punch-out', [AttendanceRecordController::class, 'punchOut'])->name('punch.out');
    Route::get('/statistics', [AttendanceRecordController::class, 'statistics'])->name('statistics');

    // Route::get('create-team', [AdminController::class, 'createTeam'])->name('create.team');

    // Route::post('team-store', [LeaderEmployeeController::class, 'store'])->name('team.store');
    Route::get('team-data-datatable', [LeaderEmployeeController::class, 'teamDatatable'])->name('data.datatable');
    // Route::get('team-delete/{id}', [LeaderEmployeeController::class, 'teamDelete'])->name('delete.team');
    Route::get('team-edit/{id}', [LeaderEmployeeController::class, 'teamEdit'])->name('edit.team');
    Route::post('team-update', [LeaderEmployeeController::class, 'update'])->name('update.team');
    Route::get('test/{id}', [ScheduleController::class, 'test']);
    Route::get('/employee-attendance-list', [AttendanceRecordController::class, 'attendanceRecordEdit'])->name('emp.edit');
    Route::get('/employee-list-attendance', [AttendanceRecordController::class, 'empployeeList'])->name('emp.list');
    Route::get('/edit-attendance/{id}', [AttendanceRecordController::class, 'ediAtttendance'])->name('edit.page');
    Route::get('/edit-attendance-employee-record/{id}', [AttendanceRecordController::class, 'ediAtttendanceRecord'])->name('edit.attendance');
    Route::post('/delete-attendance-employee-record', [AttendanceRecordController::class, 'deleteAttendance'])->name('attendance.delete');
    Route::post('/delete-attendance-single-record', [AttendanceRecordController::class, 'deleteSingleAttendance'])->name('attendance.delete.single');
    Route::get('/get-schedule/{id}', [AttendanceRecordController::class, 'getSchedule']);
    Route::post('/schedule-update', [AttendanceRecordController::class, 'updateAttendance'])->name('schedule.update');
    Route::get('/statistics-admin/{id}', [AttendanceRecordController::class, 'statisticsAdmin'])->name('statistics.emp');
    Route::post('search-record-admin', [AttendanceRecordController::class, 'searchAdmin'])->name('search.admin');
    // Attendance Ends

    // Route to Active/Deactive Users by Admin 
    Route::post('/update-employee-status', [AdminController::class, 'updateStatus'])->name('update.employee.status');

    //Route Update Employee Password
    Route::get('/get-employee-id/{id}', [AdminController::class, 'getEmployeeId'])->name('get.employee.id');
    Route::post('/update-employee-password', [AdminController::class, 'updatePassword'])->name('update.employee.password');

    // User Profile Routes
    Route::resource('user-profile', UserProfileController::class);
    // Route::get('all-employees', [UserProfileController::class, 'allEmployee'])->name('all.employees');
    // Route::get('/view-user-profile/{id}', [UserProfileController::class, 'profileShow'])->name('user-profile.customDetails');
    // Route::post('/updateVisaInfo', [UserProfileController::class, 'updateVisaInfo'])->name('update.visainfo');
    // Route::post('/emergency-update', [UserProfileController::class, 'updateEmergency'])->name('emergency.update');
    // Route::post('/dependant-update', [UserProfileController::class, 'updateDependant'])->name('dependant.update');


    // Dynamic Department Routes
    Route::resource('company', CompanyController::class);
    Route::resource('brand', BrandController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('designation', DesignationController::class);

    // Leave Application Frontend Route
    Route::get('/leave_application_form', [LeaveController::class, 'leave_form'])->name('leave.form.show');
    Route::post('/leave_application_store', [LeaveController::class, 'store_leave'])->name('leave.form.store');
    Route::get('/leave_status', [LeaveController::class, 'LeaveStatus'])->name('leave.status');
    Route::get('/leave_application/data', [LeaveController::class, 'display_leave'])->name('leave_application.data');
    Route::get('/unassigned_application', [LeaveController::class, 'UnassignedLeaveIndex'])->name('leave_application.unassigned');
    Route::post('/add_unassigned_leave', [LeaveController::class, 'AddUnassignedLeave'])->name('leave.add_unassigned');
    Route::get('/multiselect', [LeaveController::class, 'multiSelect'])->name('multiselect');
    // HR Pending Leave Working Route
    Route::get('/leave_applications/hr', [LeaveController::class, 'leave_view_hr'])->name('leave.hr_work');
    Route::get('/leave_application/hr_data', [LeaveController::class, 'display_leave_hr'])->name('leave_application.hr_data');

    // Route to fetch the data for Modal
    Route::get('/leave_application/{id}', [LeaveController::class, 'getLeaveApplication']);
    Route::post('/leave_action', [LeaveController::class, 'leave_action'])->name('leave.form.action');
    Route::post('/leave_action/hr_work_done', [LeaveController::class, 'leave_hr_workdone'])->name('leave.hr.work_done');

    // Route for Payrolls
    Route::get('/salary_deduction', [PayrollController::class, 'salary_deduction_index'])->name('payroll.salary_deduction');
    Route::get('/salary_deduction_data', [PayrollController::class, 'salary_deduction_dynamic_data'])->name('payroll.dynamic_data');



    //Route AssignLeaveApprovals Using Custom-Multi-Select
    Route::resource('leave-approvals', AssignedLeaveApprovalsController::class);
    Route::get('/searchAssigner', [AssignedLeaveApprovalsController::class, 'searchAssigner'])->name('searchAssigner');
    Route::get('/assigned-leave-approvals/get_users', [AssignedLeaveApprovalsController::class, 'getUsers']);  //GETTING USER IN DROP DOWN.
    Route::post('/assigned-leave-approvals/store', [AssignedLeaveApprovalsController::class, 'store']);  //STORING VALUES IN TABLE.
    // Web route to fetch leave approval details by ID
    Route::get('/edit/{id}', [AssignedLeaveApprovalsController::class, 'edit']);


    // Define route for Fingerprint records
    // Route::resource('fingerprint-record', FingerprintController::class);
    // Route::get('/search-users', [FingerprintController::class, 'searchUsers'])->name('search.users');

    // Single Routes 
    Route::resource('roles-permissions', RolesPermissionsController::class);
    // Route::resource('annual-leaves', AnnualLeavesController::class);
    Route::resource('expired-visa-information', ExpiredVisaInfoController::class);

    //user profile image and password update route group
    Route::controller(SettingController::class)->group(function () {
        Route::get('/settings', 'index')->name('user.settings');
        Route::post('/settings/update-image', 'updateImage')->name('settings.updateImage');
        Route::post('/update-password', [SettingController::class, 'updatePassword'])->name('update.password');
    });


    // Permission Routes
    Route::get('/user-permissions/{userId}', [RolesPermissionsController::class, 'getUserPermissions'])->name('get.user.permissions');
    Route::post('/user-permissions/{userId}', [RolesPermissionsController::class, 'saveUserPermissions'])->name('save.user.permissions');

});

require __DIR__ . '/auth.php';