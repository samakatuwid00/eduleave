<?php

use App\Http\Controllers\ActionCenterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\ExcelUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportCenterController;
use App\Http\Controllers\LeaveAnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
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

Route::get('/admin/action-center', [ActionCenterController::class, 'index'])
    ->middleware(['auth', 'admin', 'admin.permission:view_analytics'])
    ->name('admin.action-center');

Route::get('/admin/leave-analytics', [LeaveAnalyticsController::class, 'index'])
    ->middleware(['auth', 'admin', 'admin.permission:view_analytics'])
    ->name('admin.leave-analytics');

Route::middleware(['auth', 'admin', 'admin.permission:manage_imports'])->prefix('admin/import-center')->group(function () {
    Route::get('/', [ImportCenterController::class, 'index'])->name('admin.import-center');
    Route::post('/preview', [ImportCenterController::class, 'preview'])->name('admin.import-center.preview');
    Route::post('/{batch}/confirm', [ImportCenterController::class, 'confirm'])->name('admin.import-center.confirm');
    Route::post('/{batch}/rollback', [ImportCenterController::class, 'rollback'])->name('admin.import-center.rollback');
});

Route::middleware(['auth', 'admin', 'admin.permission:manage_automation'])->prefix('admin/automation')->group(function () {
    Route::get('/', [AutomationController::class, 'index'])->name('admin.automation');
    Route::put('/settings', [AutomationController::class, 'update'])->name('admin.automation.update');
    Route::post('/run', [AutomationController::class, 'run'])->name('admin.automation.run');
    Route::post('/runs/{run}/retry', [AutomationController::class, 'retry'])->name('admin.automation.retry');
});

Route::middleware(['auth', 'admin', 'admin.permission:export_reports'])->prefix('admin/reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/{report}/export', [ReportController::class, 'export'])->name('admin.reports.export');
});

Route::middleware(['auth', 'admin', 'admin.permission:view_audit'])->prefix('admin/audit')->group(function () {
    Route::get('/', [AuditController::class, 'index'])->name('admin.audit');
    Route::get('/export', [AuditController::class, 'export'])->name('admin.audit.export');
    Route::put('/administrators/{user}/role', [AuditController::class, 'updateRole'])->name('admin.audit.role');
    Route::post('/events/{event}/hold', [AuditController::class, 'hold'])->name('admin.audit.hold');
});

// User Dashboard Warning
Route::get('/user/dashboard/warning', [UserController::class, 'warning'])
    ->middleware(['auth', 'verified', 'pending'])
    ->name('/user/dashboard/warning');

// Admin - User Management Routes
Route::prefix('admin/users')->middleware(['auth', 'admin', 'admin.permission:manage_users'])->group(function () {
    Route::get('/view-all_users', [AdminController::class, 'all_users']);
    Route::get('/view-pending_users', [AdminController::class, 'pending_users']);
    Route::get('/view-approved_users', [AdminController::class, 'approved_users']);
    Route::get('/view-rejected_users', [AdminController::class, 'rejected_users']);
    Route::post('/approve/{id}', [AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/reject/{id}', [AdminController::class, 'rejectUser'])->name('admin.users.reject');
});

// Get User Details
Route::get('/get-user-details', [AdminController::class, 'getUserDetails'])
    ->middleware(['auth', 'admin', 'admin.permission:manage_users'])
    ->name('getUserDetails');

// Teachers Leave Cards
Route::get('/admin/teacher_leave_cards', [AdminController::class, 'leave_cards'])
    ->middleware(['auth', 'admin', 'admin.permission:manage_leave_cards']);

// Individual Leave Cards
Route::get('/admin/leave_card/{employee_number}', [AdminController::class, 'show'])
    ->name('leave_card.show')
    ->middleware(['auth', 'admin', 'admin.permission:manage_leave_cards']);

// Edit/Delete Rows of Leave Card
Route::middleware(['auth', 'admin'])->group(function () {
    Route::put('/admin/card_info/{cardType}/{id}', [AdminController::class, 'update'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->whereNumber('id')
        ->middleware('admin.permission:manage_leave_cards')
        ->name('admin.card-info.update');
    Route::delete('/admin/card_info/{cardType}/{id}', [AdminController::class, 'destroy'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->whereNumber('id')
        ->middleware('admin.permission:manage_leave_cards')
        ->name('admin.card-info.destroy');
    Route::post('/card-info/store', [AdminController::class, 'store'])
        ->middleware('admin.permission:manage_leave_cards');

    Route::post('/admin/leave_card/{cardType}/{employeeNumber}/import', [ExcelUploadController::class, 'upload'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->middleware('admin.permission:manage_imports')
        ->name('admin.leave-card.import');
    Route::get('/admin/leave_card/{cardType}/{employeeNumber}/template', [ExcelUploadController::class, 'downloadTemplate'])
        ->whereIn('cardType', [PersonnelType::CODE_TEACHING, PersonnelType::CODE_NON_TEACHING])
        ->middleware('admin.permission:manage_imports')
        ->name('admin.leave-card.template');
});

// Remarks
Route::get('/get-remarks', [AdminController::class, 'getRemarks'])
    ->name('get.remarks')
    ->middleware(['auth']);
