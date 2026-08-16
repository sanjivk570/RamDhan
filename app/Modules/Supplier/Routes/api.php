<?php

declare(strict_types=1);

use App\Modules\Supplier\Controllers\SupplierController;
use App\Modules\Supplier\Auth\Controllers\SupplierAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function (): void {
    // Supplier portal authentication.
    Route::prefix('supplier/auth')->group(function (): void {
        Route::post('/login', [SupplierAuthController::class, 'login']);
        Route::post('/forgot-password', [SupplierAuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [SupplierAuthController::class, 'resetPassword']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [SupplierAuthController::class, 'logout']);
            Route::post('/change-password', [SupplierAuthController::class, 'changePassword']);
            Route::get('/profile', [SupplierAuthController::class, 'profile']);
            Route::put('/profile', [SupplierAuthController::class, 'updateProfile']);
        });
    });

    // Admin supplier management.
    Route::middleware(['auth:sanctum', 'permission:supplier.view'])
        ->get('/suppliers', [SupplierController::class, 'index']);

    Route::middleware(['auth:sanctum', 'permission:supplier.view'])
        ->get('/suppliers/{uuid}', [SupplierController::class, 'show']);

    Route::middleware(['auth:sanctum', 'permission:supplier.create'])
        ->post('/suppliers', [SupplierController::class, 'store']);

    Route::middleware(['auth:sanctum', 'permission:supplier.update'])
        ->put('/suppliers/{uuid}', [SupplierController::class, 'update']);

    Route::middleware(['auth:sanctum', 'permission:supplier.update'])
        ->patch('/suppliers/{uuid}/status', [SupplierController::class, 'changeStatus']);

    Route::middleware(['auth:sanctum', 'permission:supplier.delete'])
        ->delete('/suppliers/{uuid}', [SupplierController::class, 'destroy']);

    Route::middleware(['auth:sanctum', 'permission:supplier.update'])
        ->patch('/suppliers/{uuid}/restore', [SupplierController::class, 'restore']);
});
