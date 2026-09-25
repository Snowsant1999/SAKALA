<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminMasterController;

// Auth Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout']);

// Protected Routes (using MockAuth middleware)
Route::middleware([\App\Http\Middleware\MockAuth::class])->group(function () {
    
    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);

    // Academic Module
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::get('/courses/{id}/materials', [CourseController::class, 'materials']);
    Route::post('/courses/{id}/materials', [CourseController::class, 'addMaterial']);
    Route::delete('/courses/{id}/materials/{materialId}', [CourseController::class, 'deleteMaterial']);
    Route::get('/courses/{id}/assignments', [CourseController::class, 'assignments']);
    Route::post('/courses/{id}/assignments', [CourseController::class, 'addAssignment']);
    Route::post('/courses/{id}/assignments/{assignmentId}/submit', [CourseController::class, 'submitAssignment']);
    Route::get('/schedule', [CourseController::class, 'schedule']);

    // Rooms Module
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/{id}', [RoomController::class, 'show']);

    // Reservations Module
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/create', [ReservationController::class, 'create']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);

    // Kampus Aman Module
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/create', [ReportController::class, 'create']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
        // Admin Reservations & Conflicts
        Route::get('/reservations', [ReservationController::class, 'adminIndex']);
        Route::post('/reservations/{id}/approve', [ReservationController::class, 'adminApprove']);
        Route::post('/reservations/{id}/reject', [ReservationController::class, 'adminReject']);

        // Admin Reports
        Route::get('/reports', [ReportController::class, 'adminIndex']);
        Route::post('/reports/{id}/update', [ReportController::class, 'adminUpdate']);

        // Admin Master Data
        Route::get('/users', [AdminMasterController::class, 'users']);
        Route::get('/students', [AdminMasterController::class, 'students']);
        Route::get('/lecturers', [AdminMasterController::class, 'lecturers']);
        Route::get('/departments', [AdminMasterController::class, 'departments']);
        Route::get('/study-programs', [AdminMasterController::class, 'studyPrograms']);
        Route::get('/courses', [AdminMasterController::class, 'courses']);
        Route::get('/classes', [AdminMasterController::class, 'classes']);
        Route::get('/buildings', [AdminMasterController::class, 'buildings']);
        Route::get('/rooms', [AdminMasterController::class, 'rooms']);
        Route::get('/schedules', [AdminMasterController::class, 'schedules']);
    });
});
