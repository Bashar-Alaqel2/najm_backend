<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\RoleController;

// 1. Auth & User Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Publicly accessible for UI testing
Route::get('/employees', [EmployeeController::class, 'index']);
Route::get('/roles', [RoleController::class, 'index']);
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/job-titles', [JobTitleController::class, 'index']);

// We wrap the rest in auth:sanctum (can be removed temporarily for testing)
// Route::middleware('auth:sanctum')->group(function () {
    
    // 2. Employees Routes
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

    // Roles & Operations Management
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}/permissions', [RoleController::class, 'getPermissions']);
    Route::post('/roles/{id}/permissions', [RoleController::class, 'savePermissions']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);

    // 2.1 Departments Routes
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

    // 2.2 Job Titles Routes
    Route::post('/job-titles', [JobTitleController::class, 'store']);
    Route::put('/job-titles/{id}', [JobTitleController::class, 'update']);
    Route::delete('/job-titles/{id}', [JobTitleController::class, 'destroy']);

    // 3. Permits Routes
    Route::get('/permits', [PermitController::class, 'index']);
    Route::post('/permits', [PermitController::class, 'store']); // طلب تصريح
    Route::post('/permits/{id}/approve', [PermitController::class, 'approve']);
    Route::post('/permits/{id}/reject', [PermitController::class, 'reject']);
    Route::post('/permits/{id}/record-exit', [PermitController::class, 'recordExit']);
    Route::post('/permits/{id}/record-return', [PermitController::class, 'recordReturn']);

    // 4. Visitors Routes
    Route::get('/visitors', [VisitorController::class, 'index']);
    Route::post('/visitors/invite', [VisitorController::class, 'createInvitation']); // إنشاء دعوة
    Route::post('/visitors/register', [VisitorController::class, 'register']); // تسجيل زائر بدون موعد أو بباركود
    Route::post('/visitors/{id}/checkout', [VisitorController::class, 'checkout']); // تسجيل خروج

    // 5. Vehicles Routes
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::post('/vehicles/request-entry', [VehicleController::class, 'requestEntry']); // الحارس يطلب دخول
    Route::post('/vehicles/{id}/approve', [VehicleController::class, 'approveEntry']); // الإدارة توافق
    Route::post('/vehicles/{id}/log-movement', [VehicleController::class, 'logMovement']); // تسجيل دخول/خروج فعلي

    // 6. Maintenance & Procurement Routes
    Route::get('/maintenance-requests', [MaintenanceController::class, 'index']);
    Route::post('/maintenance-requests', [MaintenanceController::class, 'store']); // إنشاء طلب
    Route::post('/maintenance-requests/{id}/approve', [MaintenanceController::class, 'approve']);
    Route::post('/maintenance-requests/{id}/assign-purchaser', [MaintenanceController::class, 'assignPurchaser']);
    Route::post('/maintenance-requests/{id}/execute-purchase', [MaintenanceController::class, 'executePurchase']);
    Route::post('/maintenance-requests/{id}/verify-gate', [MaintenanceController::class, 'verifyAtGate']);
    Route::post('/maintenance-requests/{id}/confirm-receipt', [MaintenanceController::class, 'confirmReceipt']);
// });
