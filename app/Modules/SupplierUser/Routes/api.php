<?php

declare(strict_types=1);

use App\Modules\SupplierUser\Controllers\SupplierUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Middleware\SubstituteBindings;

Route::prefix('api/v1')->middleware(['auth:sanctum', SubstituteBindings::class])->group(function (): void {
    Route::prefix('suppliers/{supplier}/users')->group(function (): void {
        Route::middleware('permission:supplier.user.view')
            ->get('/', [SupplierUserController::class, 'index']);

        Route::middleware('permission:supplier.user.view')
            ->get('/{uuid}', [SupplierUserController::class, 'show']);

        Route::middleware('permission:supplier.user.create')
            ->post('/', [SupplierUserController::class, 'store']);

        Route::middleware('permission:supplier.user.update')
            ->put('/{uuid}', [SupplierUserController::class, 'update']);

        Route::middleware('permission:supplier.user.update')
            ->patch('/{uuid}/status', [SupplierUserController::class, 'changeStatus']);

        Route::middleware('permission:supplier.user.delete')
            ->delete('/{uuid}', [SupplierUserController::class, 'destroy']);

        Route::middleware('permission:supplier.user.update')
            ->patch('/{uuid}/restore', [SupplierUserController::class, 'restore']);
    });
});
