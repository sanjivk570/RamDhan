<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Shipment\Controllers\ShipmentController;
use App\Modules\Shipment\Controllers\Admin\ShipmentController as AdminShipmentController;

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:customer')->group(function () {
        Route::get('shipments', [ShipmentController::class, 'index']);
        Route::get('shipments/{uuid}', [ShipmentController::class, 'show']);
    });

    Route::prefix('admin/shipments')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [AdminShipmentController::class, 'index'])
            ->middleware('permission:shipment.view');
        Route::get('/{uuid}', [AdminShipmentController::class, 'show'])
            ->middleware('permission:shipment.view');
        Route::post('/', [AdminShipmentController::class, 'store'])
            ->middleware('permission:shipment.create');
        Route::put('/{uuid}', [AdminShipmentController::class, 'update'])
            ->middleware('permission:shipment.update');
        Route::patch('/{uuid}/ship', [AdminShipmentController::class, 'ship'])
            ->middleware('permission:shipment.update');
    });
});
