<?php

use App\Enums\UserRole;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClinicChatController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\HomeContentController;
use App\Models\User;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SiteOverrideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('ping', fn () => response()->json([
    'message' => 'Laravel API works',
]));

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

Route::get('home-content', [HomeContentController::class, 'show']);
Route::get('departments', [DepartmentController::class, 'index']);
Route::get('departments/{slug}', [DepartmentController::class, 'show']);
Route::get('doctors', [DoctorController::class, 'index']);
Route::get('doctors/{doctor}', [DoctorController::class, 'show']);
Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{service}', [ServiceController::class, 'show']);
Route::get('feedback', [FeedbackController::class, 'index']);
Route::post('contact', [ContactController::class, 'store']);
Route::post('ai-chat', [AiChatController::class, 'store']);
Route::post('clinic-chat', [ClinicChatController::class, 'store']);
Route::get('site-overrides', [SiteOverrideController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::match(['PUT', 'PATCH', 'POST'], 'profile', [AuthController::class, 'updateProfile']);

    Route::apiResource('appointments', AppointmentController::class);
    Route::post('feedback', [FeedbackController::class, 'store']);

    Route::get('conversations', [ConversationController::class, 'index']);
    Route::get('conversations/{conversation}', [ConversationController::class, 'show']);
    Route::delete('conversations/{conversation}', [ConversationController::class, 'destroy']);

    Route::middleware('admin')->group(function () {
        Route::match(['PUT', 'POST'], 'admin/site-overrides', [SiteOverrideController::class, 'update']);
        Route::post('departments', [DepartmentController::class, 'store']);
        Route::match(['PUT', 'POST'], 'departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy']);

        Route::post('doctors', [DoctorController::class, 'store']);
        Route::put('doctors/{doctor}', [DoctorController::class, 'update']);
        Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy']);

        Route::post('services', [ServiceController::class, 'store']);
        Route::match(['PUT', 'POST'], 'services/{service}', [ServiceController::class, 'update']);
        Route::delete('services/{service}', [ServiceController::class, 'destroy']);

        Route::get('patients', [PatientController::class, 'index']);
        Route::get('users', [AdminUserController::class, 'index']);
        Route::match(['PUT', 'PATCH', 'POST'], 'users/{user}/role', [AdminUserController::class, 'updateRole']);
        Route::post('users/role', [AdminUserController::class, 'updateRoleByEmail']);

        Route::prefix('dashboard')->group(function () {
            Route::get('home-content', [HomeContentController::class, 'show']);
            Route::put('home-content', [HomeContentController::class, 'update']);
            Route::get('stats', [DashboardController::class, 'stats']);
            Route::get('recent-appointments', [DashboardController::class, 'recentAppointments']);
            Route::get('recent-users', [DashboardController::class, 'recentUsers']);
            Route::get('conversations', [DashboardController::class, 'conversations']);
        });
    });
});

