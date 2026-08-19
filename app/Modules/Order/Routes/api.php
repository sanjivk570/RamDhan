<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Order\Controllers\OrderController;
use App\Modules\Order\Controllers\Admin\OrderController as AdminOrderController;

Route::prefix('api/v1')->group(function () {
    Route::post('orders/checkout', [OrderController::class, 'checkout']);
    Route::post('orders/checkout/preview', [OrderController::class, 'preview']);
    Route::get('guest/orders/{orderNumber}', [OrderController::class, 'guestShow']);

    Route::middleware('auth:customer')->group(function () {
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{uuid}', [OrderController::class, 'show']);
        Route::post('orders/{uuid}/cancel', [OrderController::class, 'cancel']);
        Route::post('orders/{uuid}/reorder', [OrderController::class, 'reorder']);
    });

    Route::prefix('admin/orders')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])
            ->middleware('permission:order.view');
        Route::get('/{uuid}', [AdminOrderController::class, 'show'])
            ->middleware('permission:order.view');
        Route::patch('/{uuid}/status', [AdminOrderController::class, 'status'])
            ->middleware('permission:order.update');
    });
});
