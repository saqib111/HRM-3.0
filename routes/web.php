<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\{

    AdminController,
    ProfileController,
    UserProfileController,
    CompanyController,
    ScheduleController,
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
    Route::get('/get-employees', [AdminController::class, 'getEmployee'])->name('list.employee');
    Route::get('/get-employee/{id}', [AdminController::class, 'editEmployee'])->name('edit.employee');
    Route::post('/update-employee', [AdminController::class, 'updateEmployee'])->name('update.employee');
    Route::delete('/delete-employee/{id}', [AdminController::class, 'deleteEmployee'])->name('delete.employee');
    Route::get('/check-designation/{id}', [AdminController::class, 'checkDesignation'])->name('check.designation');
    //schedule start
    Route::get('schedule-list', [ScheduleController::class, 'index'])->name('schedule');
    //schedule end

    // Route to Active/Deactive Users by Admin 
    Route::post('/update-employee-status', [AdminController::class, 'updateStatus'])->name('update.employee.status');


    Route::resource('user-profile', UserProfileController::class);

    Route::resource('company', CompanyController::class);
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::delete('/company/{company}', [CompanyController::class, 'destroy'])->name('company.destroy');

});

require __DIR__ . '/auth.php';