<?php

use App\Modules\Slider\Controllers\SliderController;
use App\Modules\Slider\Controllers\Storefront\StorefrontSliderController;
use Illuminate\Support\Facades\Route;

/**
 * Slider module API routes.
 *
 * Defines API endpoints for managing sliders and their items
 * (admin, permission-gated) as well as a public storefront
 * endpoint used to render sliders on the frontend.
 *
 * @package App\Modules\Slider\Routes
 * @author Sanjiv Kumar Kushwaha
 */
Route::prefix('api/v1/storefront')->group(function () {
    Route::get('/sliders/{code}', [StorefrontSliderController::class, 'show']);
});

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:sanctum')->prefix('sliders')->group(function () {
        Route::get('/', [SliderController::class, 'index'])->middleware('permission:slider.view');
        Route::post('/', [SliderController::class, 'store'])->middleware('permission:slider.create');
        Route::get('/{uuid}', [SliderController::class, 'show'])->middleware('permission:slider.view');
        Route::put('/{uuid}', [SliderController::class, 'update'])->middleware('permission:slider.update');
        Route::patch('/{uuid}/status', [SliderController::class, 'changeStatus'])->middleware('permission:slider.update');
        Route::delete('/{uuid}', [SliderController::class, 'destroy'])->middleware('permission:slider.delete');
        Route::post('/{uuid}/restore', [SliderController::class, 'restore'])->middleware('permission:slider.restore');

        // Slide items (children)
        Route::post('/{uuid}/items', [SliderController::class, 'storeItem'])->middleware('permission:slider.update');
        Route::put('/{uuid}/items/{itemUuid}', [SliderController::class, 'updateItem'])->middleware('permission:slider.update');
        Route::delete('/{uuid}/items/{itemUuid}', [SliderController::class, 'destroyItem'])->middleware('permission:slider.update');
    });
});
