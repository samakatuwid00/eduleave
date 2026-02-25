<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    UserController,
    AdminController,
    ProfileController,
    ResetPasswordController,
    ExcelUploadController,
};
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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
require __DIR__ . '/auth.php';

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
    Route::put('/admin/card_info/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/card_info/{id}', [AdminController::class, 'destroy']);
    Route::post('/card-info/store', [AdminController::class, 'store']);
});

// Remarks
Route::get('/get-remarks', [AdminController::class, 'getRemarks'])
    ->name('get.remarks')
    ->middleware(['auth']);

// Forgot Password (view form)
Route::view('/forgot-password', 'auth.forgot-password')->name('pass.request');

// Send password reset link (POST request)
Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail']);

// Reset Password Form (GET request)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'passwordReset'])->name('password.reset');

// Update password (POST request)
Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->name('pass.update');

// Email Verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [ResetPasswordController::class, 'verifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [ResetPasswordController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/verification-notification', [ResetPasswordController::class, 'verifySend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

//Approve and Reject Email Notification
Route::post('/admin/users/send-approval-email/{userId}', [ResetPasswordController::class, 'sendApprovalEmail']);
Route::post('/admin/users/send-rejection-email/{userId}', [ResetPasswordController::class, 'sendRejectionEmail']);

// Route::post('register-again/{user_id}', [RegisteredUserController::class, 'registerAgain'])->name('register-again');
// Upload Excel File
Route::post('/upload-excel/{employee_number}', [ExcelUploadController::class, 'upload'])->name('upload-excel');

// Download Excel Template
Route::get('/admin/leave_card/template/{employee_number}', [ExcelUploadController::class, 'downloadTemplate'])->name('download-template');
