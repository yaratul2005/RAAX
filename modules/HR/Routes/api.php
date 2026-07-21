<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\AttendanceController;
use Modules\HR\Http\Controllers\EmployeeController;
use Modules\HR\Http\Controllers\ShiftController;

Route::middleware(['api', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/hr/employees', [EmployeeController::class, 'store']);
    Route::get('/hr/employees', [EmployeeController::class, 'index']);
    Route::delete('/hr/employees/{id}', [EmployeeController::class, 'destroy']);

    Route::post('/hr/shifts', [ShiftController::class, 'store']);
    Route::get('/hr/shifts', [ShiftController::class, 'index']);

    Route::post('/hr/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/hr/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::post('/hr/salaries', [\Modules\HR\Http\Controllers\PayrollController::class, 'storeSalaryProfile']);
    Route::post('/hr/payroll/generate', [\Modules\HR\Http\Controllers\PayrollController::class, 'generateBatch']);
    Route::post('/hr/payroll/{payslip}/pay', [\Modules\HR\Http\Controllers\PayrollController::class, 'pay']);
});
