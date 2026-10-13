<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceSessionController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::apiResource('students', StudentController::class);
Route::get('student-by-qr', [StudentController::class, 'showByQr']);
Route::apiResource('events', EventController::class);

// Attendance (list + admin manual check-in + delete)
Route::get('attendances', [AttendanceController::class, 'index']);
Route::post('attendances', [AttendanceController::class, 'store']);
Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy']);

// Secure student check-in (QR + GPS)
Route::post('attendances/verify-checkin', [AttendanceController::class, 'verifyAndCheckIn']);

// Attendance sessions (admin controls)
Route::post('attendance-sessions', [AttendanceSessionController::class, 'store']);
Route::patch('attendance-sessions/{session}/stop', [AttendanceSessionController::class, 'stop']);
Route::get('attendance-sessions/active/{eventId}', [AttendanceSessionController::class, 'activeForEvent']);

Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('student/login', [\App\Http\Controllers\Api\StudentAuthController::class, 'login']);

Route::get('analytics', [\App\Http\Controllers\Api\AnalyticsController::class, 'index']);
