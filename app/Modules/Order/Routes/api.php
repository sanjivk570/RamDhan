<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Order\Controllers\OrderController;
use App\Modules\Order\Controllers\Admin\OrderController as AdminOrderController;

Route::prefix('api/v1')->group(function () {
    Route::post('orders/checkout', [OrderController::class, 'checkout'])
        ->middleware('throttle:20,1'); // Rate limit to 20 per minute
    
    Route::post('orders/checkout/preview', [OrderController::class, 'preview'])
        ->middleware('throttle:60,1'); // Rate limit to 60 per minute
    
    Route::get('guest/orders/{orderNumber}', [OrderController::class, 'guestShow'])
        ->middleware('throttle:30,1');
    Route::get('guest/orders/{orderNumber}/summary', [OrderController::class, 'guestSummary'])
        ->middleware('throttle:30,1');

    Route::middleware('auth:customer')->group(function () {
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{uuid}', [OrderController::class, 'show']);
        Route::get('orders/{uuid}/summary', [OrderController::class, 'summary']);
        Route::post('orders/{uuid}/cancel', [OrderController::class, 'cancel'])
            ->middleware('throttle:10,1'); // Rate limit to 10 per minute
        Route::post('orders/{uuid}/reorder', [OrderController::class, 'reorder'])
            ->middleware('throttle:10,1'); // Rate limit to 10 per minute
    });

    Route::prefix('admin/orders')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])
            ->middleware('permission:order.view');
        Route::get('/{uuid}', [AdminOrderController::class, 'show'])
            ->middleware('permission:order.view');
        Route::get('/{uuid}/summary', [AdminOrderController::class, 'summary'])
            ->middleware('permission:order.view');
        Route::patch('/{uuid}/status', [AdminOrderController::class, 'status'])
            ->middleware('permission:order.update');
    });
});
