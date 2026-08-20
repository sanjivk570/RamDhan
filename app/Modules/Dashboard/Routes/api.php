<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index']
        )->middleware('permission:dashboard.view');

    });
});