<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Unit\Controllers\UnitController;

/**
 * Unit module API routes.
 *
 * Defines API endpoints for unit management including
 * listing, creating, updating, and deleting units.
 * Routes are protected using Sanctum authentication
 * and permission-based authorization.
 *
 * @package App\Modules\Unit\Routes
 * @author Sanjiv Kumar Kushwaha
 */
Route::prefix('api/v1')->group(function () {
   Route::prefix('units')->group(function () {

    Route::get('/',[UnitController::class, 'index']);

    Route::get('/{uuid}', [UnitController::class, 'show']);

    Route::post('/', [UnitController::class, 'store']);

    Route::put('/{uuid}', [UnitController::class, 'update']);

    Route::patch('/{uuid}/status', [UnitController::class, 'changeStatus']);

    Route::delete('/{uuid}', [UnitController::class, 'destroy']);

    Route::post('/{uuid}/restore', [UnitController::class, 'restore']);
});

});

