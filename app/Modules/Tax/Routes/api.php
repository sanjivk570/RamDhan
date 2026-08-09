<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Tax\Controllers\TaxClassController;
use App\Modules\Tax\Controllers\TaxRateController;

/**
 * tax module API routes.
 *
 * Defines API endpoints for tax management including
 * listing, creating, updating, and deleting taxs.
 * Routes are protected using Sanctum authentication
 * and permission-based authorization.
 *
 * @package App\Modules\Tax\Routes
 * @author Sanjiv Kumar Kushwaha
 */
Route::prefix('api/v1')->group(function () {
   Route::prefix('tax-classes')->group(function () {
       Route::get('/', [TaxClassController::class, 'index']);

       Route::get('/{uuid}', [TaxClassController::class, 'show']);

       Route::post('/', [TaxClassController::class, 'store']);

       Route::put('/{uuid}', [TaxClassController::class, 'update']);

       Route::patch('/{uuid}/status', [TaxClassController::class, 'changeStatus']);

       Route::delete('/{uuid}', [TaxClassController::class, 'destroy']);

       Route::post('/{uuid}/restore', [TaxClassController::class, 'restore']);
   });


   Route::prefix('tax-rates')->group(function () {

       Route::get('/', [TaxRateController::class, 'index']);

       Route::get('/{uuid}', [TaxRateController::class, 'show']);

       Route::post('/', [TaxRateController::class, 'store']);

       Route::put('/{uuid}', [TaxRateController::class, 'update']);

       Route::patch('/{uuid}/status', [TaxRateController::class, 'changeStatus']);

       Route::delete('/{uuid}', [TaxRateController::class, 'destroy']);

       Route::post('/{uuid}/restore',[TaxRateController::class, 'restore']
       
       );
   });


});

