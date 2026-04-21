<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;

// Routes yang tidak memerlukan auth
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('login', [SuperAdminAuthController::class, 'loginForm'])->name('login');
    Route::post('login', [SuperAdminAuthController::class, 'login'])->name('login.store');

    Route::get('verify-email', [SuperAdminAuthController::class, 'verificationNotice'])
        ->name('verification.notice');
    Route::post('verify-email/resend', [SuperAdminAuthController::class, 'resendVerificationEmail'])
        ->name('verification.send');
    Route::get('verify-email/{id}/{hash}', [SuperAdminAuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

// Routes yang memerlukan auth super admin
Route::middleware(['auth', 'is_super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Dashboard
    Route::get('dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Pricing Management
    Route::prefix('pricing')->name('pricing.')->group(function () {
        Route::get('/', [SuperAdminController::class, 'pricingIndex'])->name('index');
        Route::get('/{pricing}/edit', [SuperAdminController::class, 'pricingEdit'])->name('edit');
        Route::put('/{pricing}', [SuperAdminController::class, 'pricingUpdate'])->name('update');
    });

    // Activity Logs
    Route::get('activity-logs', [SuperAdminController::class, 'activityLogs'])->name('activity-logs');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [SuperAdminController::class, 'users'])->name('index');
        Route::get('/{user}', [SuperAdminController::class, 'userShow'])->name('show');
    });

    // Revenue Reports
    Route::get('revenue', [SuperAdminController::class, 'revenue'])->name('revenue');

    // Account Settings
    Route::get('change-password', [SuperAdminAuthController::class, 'changePasswordForm'])->name('change-password');
    Route::post('change-password', [SuperAdminAuthController::class, 'changePassword'])->name('change-password.store');

    // Logout
    Route::post('logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
});
