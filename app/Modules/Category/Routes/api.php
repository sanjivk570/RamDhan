<?php

use App\Modules\Category\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->middleware('permission:category.view');
        Route::post('/', [CategoryController::class, 'store'])->middleware('permission:category.create');
        Route::get('/{uuid}', [CategoryController::class, 'show'])->middleware('permission:category.view');
        Route::put('/{uuid}', [CategoryController::class, 'update'])->middleware('permission:category.update');
        Route::patch('/{uuid}/status', [CategoryController::class, 'status'])->middleware('permission:category.update');
        Route::delete('/{uuid}', [CategoryController::class, 'destroy'])->middleware('permission:category.delete');
        Route::post('/{uuid}/restore', [CategoryController::class, 'restore'])->middleware('permission:category.restore');
    });
});
