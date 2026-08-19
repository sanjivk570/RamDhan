<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\SalesReturn\Controllers\SalesReturnController;
use App\Modules\SalesReturn\Controllers\Admin\SalesReturnController as AdminReturnController;

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:customer')->group(function () {
        Route::get('returns', [SalesReturnController::class, 'index']);
        Route::post('returns', [SalesReturnController::class, 'store']);
        Route::get('returns/{uuid}', [SalesReturnController::class, 'show']);
    });

    Route::prefix('admin/returns')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [AdminReturnController::class, 'index'])
            ->middleware('permission:return.view');
        Route::patch('/{uuid}/process', [AdminReturnController::class, 'process'])
            ->middleware('permission:return.process');
    });
});
