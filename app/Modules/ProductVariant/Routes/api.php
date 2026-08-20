<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ProductVariant\Controllers\ProductVariantController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('api/v1')->group(function () {
        Route::prefix('products/{productUuid}/variants')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index']);
            Route::post('/', [ProductVariantController::class, 'store']);
            Route::get('/{variantUuid}', [ProductVariantController::class, 'show']);
            Route::put('/{variantUuid}', [ProductVariantController::class, 'update']);
            Route::delete('/{variantUuid}', [ProductVariantController::class, 'destroy']);
            Route::patch('/{variantUuid}/default', [ProductVariantController::class, 'setDefault']);
        });
    
    });
});