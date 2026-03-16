<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AnnouncementController;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login/auth', [AuthController::class, 'Auth'])->name('auth');
Route::post('/register/create', [AuthController::class, 'registration'])->name('register.auth');

Route::middleware('auth')->group(function () {

    Route::get('/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::post('/invite/{group}/{role}', [InvitationController::class, 'generate']);
    Route::post('/join', [InvitationController::class, 'join']);
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.pages');
    Route::post('/payment/snap-token', [PaymentController::class, 'snapToken']);
    Route::get('/groups/{group}', [GroupController::class, 'show']);
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups/{group}/generate-code', [GroupController::class, 'generateCode']);
    Route::post('/payment/sync-bots', [PaymentController::class, 'syncBotsManual']);

    // routes/web.php

    Route::post('/groups/{group}/announcements', [AnnouncementController::class, 'store']);
    Route::get('/groups/{group}/announcements/{announcement}/edit', [AnnouncementController::class, 'edit']);
    Route::put('/groups/{group}/announcements/{announcement}', [AnnouncementController::class, 'update']);
    Route::delete('/groups/{group}/announcements/{announcement}', [AnnouncementController::class, 'destroy']);
});
