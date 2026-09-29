<?php

use App\Http\Controllers\AdminMasterController;
use App\Http\Controllers\AdminMasterDataController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScheduleExceptionController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth'])->group(function () {

    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->middleware('admin');

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
    Route::post('/schedules/{schedule}/exceptions', [ScheduleExceptionController::class, 'store']);
    Route::delete('/schedules/{schedule}/exceptions/{exception}', [ScheduleExceptionController::class, 'destroy']);

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
    Route::get('/reports/{id}/attachment', [ReportController::class, 'downloadAttachment']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);

    // Layanan Aspirasi Module (Separated from Kampus Aman)
    Route::get('/aspirations', [AspirationController::class, 'index']);
    Route::get('/aspirations/create', [AspirationController::class, 'create']);
    Route::post('/aspirations', [AspirationController::class, 'store']);
    Route::get('/aspirations/{id}', [AspirationController::class, 'show']);

    // Notifications Module
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // User Profile Module
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']);

    // Admin Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        // Admin Reservations & Conflicts
        Route::get('/reservations', [ReservationController::class, 'adminIndex']);
        Route::post('/reservations/{id}/approve', [ReservationController::class, 'adminApprove']);
        Route::post('/reservations/{id}/reject', [ReservationController::class, 'adminReject']);

        // Admin Reports (Kampus Aman)
        Route::get('/reports', [ReportController::class, 'adminIndex']);
        Route::post('/reports/{id}/update', [ReportController::class, 'adminUpdate']);

        // Admin Aspirations (Layanan Sarpras & Aspirasi)
        Route::get('/aspirations', [AspirationController::class, 'adminIndex']);
        Route::post('/aspirations/{id}/update', [AspirationController::class, 'adminUpdate']);

        // Admin Master Data
        Route::get('/users', [AdminMasterController::class, 'users']);
        Route::get('/students', [AdminMasterController::class, 'students']);
        Route::get('/lecturers', [AdminMasterController::class, 'lecturers']);
        Route::get('/departments', [AdminMasterController::class, 'departments']);
        Route::get('/study-programs', [AdminMasterController::class, 'studyPrograms']);
        Route::get('/courses', [AdminMasterController::class, 'courses']);
        Route::get('/classes', [AdminMasterController::class, 'classes']);
        Route::get('/cohorts', [AdminMasterController::class, 'cohorts']);
        Route::get('/buildings', [AdminMasterController::class, 'buildings']);
        Route::get('/floors', [AdminMasterController::class, 'floors']);
        Route::get('/rooms', [AdminMasterController::class, 'rooms']);
        Route::get('/schedules', [AdminMasterController::class, 'schedules']);

        $masterResources = 'users|students|lecturers|departments|study-programs|courses|classes|cohorts|buildings|floors|rooms|schedules';
        Route::get('/{resource}/create', [AdminMasterDataController::class, 'create'])->where('resource', $masterResources);
        Route::post('/{resource}', [AdminMasterDataController::class, 'store'])->where('resource', $masterResources);
        Route::get('/{resource}/{id}/edit', [AdminMasterDataController::class, 'edit'])->where(['resource' => $masterResources, 'id' => '[0-9]+']);
        Route::put('/{resource}/{id}', [AdminMasterDataController::class, 'update'])->where(['resource' => $masterResources, 'id' => '[0-9]+']);
        Route::delete('/{resource}/{id}', [AdminMasterDataController::class, 'destroy'])->where(['resource' => $masterResources, 'id' => '[0-9]+']);
    });
});
