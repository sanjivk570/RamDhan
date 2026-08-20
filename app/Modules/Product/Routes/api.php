<?php

use App\Modules\Product\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Modules\Product\Controllers\ProductImageController;

/**
 * Product API routes.
 *
 * @author Sanjiv Kumar Kushwaha
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('api/v1')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->middleware('permission:product.view');
            Route::get('/{uuid}', [ProductController::class, 'show'])->middleware('permission:product.view');
            Route::post('/',[ProductController::class, 'store'])->middleware('permission:product.create');
            Route::put('/{uuid}',[ProductController::class, 'update'])->middleware('permission:product.update');
            Route::patch('/{uuid}/status', [ProductController::class, 'changeStatus'])->middleware('permission:product.update');
            Route::delete('/{uuid}', [ProductController::class, 'destroy'])->middleware('permission:product.delete');
            Route::post('/{uuid}/restore',[ProductController::class, 'restore'])->middleware('permission:product.restore');
            Route::delete('/{uuid}/force', [ProductController::class, 'forceDelete'])->middleware('permission:product.delete');
        });
    });
});