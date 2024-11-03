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
    //schedule start
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
    Route::resource('group', GroupController::class);
    Route::get('group-data', [GroupController::class, 'groupData'])->name('group.data');
    Route::get('group-employee', [GroupController::class, 'groupEmployee'])->name('group.employee');
    Route::get('change-employee-group', [GroupController::class, 'groupChange'])->name('group.change');
    Route::post('change-group', [GroupController::class, 'changeGroupData'])->name('changegroup.data');
    Route::get('group-member/{id}', [GroupController::class, 'groupMember'])->name('group.member');
    //schedule end

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
    // Route to fetch the data for Modal
    Route::get('/leave_application/{id}', [LeaveController::class, 'getLeaveApplication']);
    Route::post('/leave_action', [LeaveController::class, 'leave_action'])->name('leave.form.action');


    Route::resource('roles-permissions', RolesPermissionsController::class);

    Route::resource('leave-approvals', AssignedLeaveApprovalsController::class);
    Route::resource('annual-leaves', AnnualLeavesController::class);
    Route::resource('expired-visa-information', ExpiredVisaInfoController::class);

});

require __DIR__ . '/auth.php';