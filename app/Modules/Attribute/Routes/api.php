<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Attribute\Controllers\AttributeController;
use App\Modules\Attribute\Controllers\AttributeValueController;

Route::prefix('api/v1')->group(function () {
    Route::prefix('attributes')
    ->group(function () {

        Route::get('/', [AttributeController::class, 'index']);
        Route::post('/', [AttributeController::class, 'store']);
        Route::get('/{uuid}', [AttributeController::class, 'show']);
        Route::put('/{uuid}', [AttributeController::class, 'update']);
        Route::delete('/{uuid}', [AttributeController::class, 'destroy']);
        Route::post('/{uuid}/restore', [AttributeController::class, 'restore']);

        /*
         * Attribute Values.
         */
        Route::prefix('{attributeUuid}/values')->group(function () {
                Route::post('/', [AttributeValueController::class, 'store']                );
                Route::get('/{valueUuid}', [AttributeValueController::class, 'show']);
                Route::put('/{valueUuid}', [AttributeValueController::class, 'update']);
                Route::delete('/{valueUuid}', [AttributeValueController::class, 'destroy']);
                Route::post('/{valueUuid}/restore', [AttributeValueController::class, 'restore']);
            });
    });
});