<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ExcelUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;
use App\Models\PersonnelType;
use Illuminate\Support\Facades\Route;

// Redirect root URL to welcome page
Route::redirect('/', '/welcome');

// Welcome Page
Route::get('/welcome', [HomeController::class, 'home'])->name('welcome');

// User Dashboard
Route::get('/user/dashboard', [UserController::class, 'userDashboard'])
    ->middleware(['auth', 'verified', 'not_admin'])
    ->name('user/dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Authentication Routes
require __DIR__.'/auth.php';

// Admin Dashboard
Route::get('/admin/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

// User Dashboard Warning
Route::get('/user/dashboard/warning', [UserController::class, 'warning'])
    ->middleware(['auth', 'verified', 'pending'])
    ->name('/user/dashboard/warning');

// Admin - User Management Routes
Route::prefix('admin/users')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/view-all_users', [AdminController::class, 'all_users']);
    Route::get('/view-pending_users', [AdminController::class, 'pending_users']);
    Route::get('/view-approved_users', [AdminController::class, 'approved_users']);
    Route::get('/view-rejected_users', [AdminController::class, 'rejected_users']);
    Route::post('/approve/{id}', [AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/reject/{id}', [AdminController::class, 'rejectUser'])->name('admin.users.reject');
});

// Get User Details
Route::get('/get-user-details', [AdminController::class, 'getUserDetails'])->name('getUserDetails');

// Teachers Leave Cards
Route::get('/admin/teacher_leave_cards', [AdminController::class, 'leave_cards'])
    ->middleware(['auth', 'admin']);

// Individual Leave Cards
Route::get('/admin/leave_card/{employee_number}', [AdminController::class, 'show'])
    ->name('leave_card.show')
    ->middleware(['auth', 'admin']);

// Edit/Delete Rows of Leave Card
Route::middleware(['auth', 'admin'])->group(function () {
    Route::put('/admin/card_info/{cardType}/{id}', [AdminController::class, 'update'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->whereNumber('id')
        ->name('admin.card-info.update');
    Route::delete('/admin/card_info/{cardType}/{id}', [AdminController::class, 'destroy'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->whereNumber('id')
        ->name('admin.card-info.destroy');
    Route::post('/card-info/store', [AdminController::class, 'store']);

    Route::post('/admin/leave_card/{cardType}/{employeeNumber}/import', [ExcelUploadController::class, 'upload'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->name('admin.leave-card.import');
    Route::get('/admin/leave_card/{cardType}/{employeeNumber}/template', [ExcelUploadController::class, 'downloadTemplate'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->name('admin.leave-card.template');
});

// Remarks
Route::get('/get-remarks', [AdminController::class, 'getRemarks'])
    ->name('get.remarks')
    ->middleware(['auth']);

// Email Verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [ResetPasswordController::class, 'verifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [ResetPasswordController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/verification-notification', [ResetPasswordController::class, 'verifySend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

// Approve and Reject Email Notification
Route::post('/admin/users/send-approval-email/{userId}', [ResetPasswordController::class, 'sendApprovalEmail']);
Route::post('/admin/users/send-rejection-email/{userId}', [ResetPasswordController::class, 'sendRejectionEmail']);

// Route::post('register-again/{user_id}', [RegisteredUserController::class, 'registerAgain'])->name('register-again');
