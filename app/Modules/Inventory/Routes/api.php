<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Inventory\Controllers\InventoryController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
        Route::prefix('inventory')->controller(InventoryController::class)->group(function () {
            Route::get('/', [InventoryController::class, 'index']);
            Route::get('/{uuid}', [InventoryController::class, 'show']);
            Route::post('/{uuid}/stock-in', [InventoryController::class, 'stockIn']);
            Route::post('/{uuid}/stock-out', [InventoryController::class, 'stockOut']);
            Route::post('/{uuid}/adjust', [InventoryController::class, 'adjust']);
            Route::get('/{uuid}/transactions', [InventoryController::class, 'transactions']);
        });
    
    });
});