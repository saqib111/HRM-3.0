<?php


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

    Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [AdminController::class, 'showEmployee'])->name('employees');
    Route::get('/employees-list', [AdminController::class, 'showEmployeeList'])->name('employees-list');
    Route::post('/add-employee', [AdminController::class, 'addEmployee'])->name('add.employee');
    Route::get('/manage-employees', [AdminController::class, 'getEmployee'])->name('list.employee');
    Route::get('/get-employee/{id}', [AdminController::class, 'editEmployee'])->name('edit.employee');
    Route::post('/update-employee', [AdminController::class, 'updateEmployee'])->name('update.employee');
    Route::delete('/delete-employee/{id}', [AdminController::class, 'deleteEmployee'])->name('delete.employee');
    Route::get('/check-designation/{id}', [AdminController::class, 'checkDesignation'])->name('check.designation');
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

    Route::get('create-team', [AdminController::class, 'createTeam'])->name('create.team');
    Route::get('team-data', [LeaderEmployeeController::class, 'teamData'])->name('team.data');
    Route::post('team-store', [LeaderEmployeeController::class, 'store'])->name('team.store');
    Route::get('team-data-datatable', [LeaderEmployeeController::class, 'teamDatatable'])->name('data.datatable');
    Route::get('team-delete/{id}', [LeaderEmployeeController::class, 'teamDelete'])->name('delete.team');
    Route::get('team-edit/{id}', [LeaderEmployeeController::class, 'teamEdit'])->name('edit.team');
    Route::post('team-update', [LeaderEmployeeController::class, 'update'])->name('update.team');
    Route::get('test/{id}', [ScheduleController::class, 'test']);
    // Attendance Ends

    // Route to Active/Deactive Users by Admin 
    Route::post('/update-employee-status', [AdminController::class, 'updateStatus'])->name('update.employee.status');

    //Route Update Employee Password
    Route::get('/get-employee-id/{id}', [AdminController::class, 'getEmployeeId'])->name('get.employee.id');
    Route::post('/update-employee-password', [AdminController::class, 'updatePassword'])->name('update.employee.password');

    // User Profile Routes
    Route::resource('user-profile', UserProfileController::class);
    Route::get('all-employees', [UserProfileController::class, 'allEmployee'])->name('all.employees');
    Route::get('/view-user-profile/{id}', [UserProfileController::class, 'profileShow'])->name('user-profile.customDetails');
    Route::post('/updateVisaInfo', [UserProfileController::class, 'updateVisaInfo'])->name('update.visainfo');
    Route::post('/emergency-update', [UserProfileController::class, 'updateEmergency'])->name('emergency.update');
    Route::post('/dependant-update', [UserProfileController::class, 'updateDependant'])->name('dependant.update');


    // Dynamic Department Routes
    Route::resource('company', CompanyController::class);
    Route::resource('brand', BrandController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('designation', DesignationController::class);

    // Leave Application Frontend Route
    Route::get('/leave_application_form', [LeaveController::class, 'leave_form'])->name('leave.form.show');
    Route::post('/leave_application_store', [LeaveController::class, 'store_leave'])->name('leave.form.store');
    Route::get('/leave_application/data', [LeaveController::class, 'display_leave'])->name('leave_application.data');
    Route::get('/unassigned_application', [LeaveController::class, 'UnassignedLeaveIndex'])->name('leave_application.unassigned');
    Route::post('/add_unassigned_leave', [LeaveController::class, 'AddUnassignedLeave'])->name('leave.add_unassigned');

    // Route to fetch the data for Modal
    Route::get('/leave_application/{id}', [LeaveController::class, 'getLeaveApplication']);
    Route::post('/leave_action', [LeaveController::class, 'leave_action'])->name('leave.form.action');


    Route::resource('roles-permissions', RolesPermissionsController::class);

    Route::resource('leave-approvals', AssignedLeaveApprovalsController::class);
    Route::resource('annual-leaves', AnnualLeavesController::class);
    Route::resource('expired-visa-information', ExpiredVisaInfoController::class);

});

require __DIR__ . '/auth.php';