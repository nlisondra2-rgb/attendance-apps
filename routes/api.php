<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceSessionController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FineController;
use App\Http\Controllers\Api\StudentAuthController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('student/login', [StudentAuthController::class, 'login']);

// Students
Route::apiResource('students', StudentController::class);
Route::get('student-by-qr', [StudentController::class, 'showByQr']);

// Student Portal Specific
Route::get('students/{studentId}/events', [EventController::class, 'studentEvents']);
Route::get('students/{studentId}/attendance-summary', [AttendanceController::class, 'studentSummary']);
Route::get('students/{studentId}/fines', [FineController::class, 'studentFines']);
Route::delete('students/{studentId}/fines/{fine}', [FineController::class, 'destroyStudentFine']);
Route::post('students/{studentId}/fines/{fine}/dispute', [FineController::class, 'submitDispute']);

// Events
Route::apiResource('events', EventController::class);
Route::patch('events/{event}/status', [EventController::class, 'changeStatus']);
Route::get('events/{event}/participants', [EventController::class, 'participants']);
Route::post('events/{event}/finalize-absences', [AttendanceController::class, 'finalizeEvent']);

// Attendance
Route::get('attendances', [AttendanceController::class, 'index']);
Route::post('attendances', [AttendanceController::class, 'store']);
Route::patch('attendances/{attendance}', [AttendanceController::class, 'update']);
Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy']);
Route::post('attendances/verify-checkin', [AttendanceController::class, 'verifyAndCheckIn']);

// Attendance Sessions (QR Tokens)
Route::post('attendance-sessions', [AttendanceSessionController::class, 'store']);
Route::patch('attendance-sessions/{session}/stop', [AttendanceSessionController::class, 'stop']);
Route::get('attendance-sessions/active/{eventId}', [AttendanceSessionController::class, 'activeForEvent']);

// Fines & Excuses Management
Route::get('admin/student-fines-summary', [FineController::class, 'studentFinesSummary']);
Route::post('admin/students/{studentId}/mark-fines-paid', [FineController::class, 'markStudentFinesPaid']);
Route::get('fines', [FineController::class, 'index']);
Route::get('fines/{fine}', [FineController::class, 'show']);
Route::patch('fines/{fine}/record-payment', [FineController::class, 'recordPayment']);
Route::patch('fines/{fine}/waive', [FineController::class, 'waive']);
Route::patch('fines/{fine}/review', [FineController::class, 'review']);

Route::get('fine-excuses', [FineController::class, 'listExcuses']);
Route::patch('fine-excuses/{excuse}/review', [FineController::class, 'reviewExcuse']);

// Analytics
Route::get('analytics', [AnalyticsController::class, 'index']);
