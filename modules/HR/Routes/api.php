<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\AttendanceController;
use Modules\HR\Http\Controllers\EmployeeController;
use Modules\HR\Http\Controllers\ShiftController;

Route::middleware('tenant')->group(function () {
    Route::post('/hr/employees', [EmployeeController::class, 'store']);
    Route::get('/hr/employees', [EmployeeController::class, 'index']);
    Route::delete('/hr/employees/{id}', [EmployeeController::class, 'destroy']);

    Route::post('/hr/shifts', [ShiftController::class, 'store']);
    Route::get('/hr/shifts', [ShiftController::class, 'index']);

    Route::post('/hr/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/hr/attendance/check-out', [AttendanceController::class, 'checkOut']);
});
