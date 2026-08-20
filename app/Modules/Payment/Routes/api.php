<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Payment\Controllers\PaymentController;
use App\Modules\Payment\Controllers\Admin\PaymentController as AdminPaymentController;

Route::prefix('api/v1')->group(function () {
    Route::post('payments/webhook', [PaymentController::class, 'webhook'])
        ->middleware('throttle:30,1') // Rate limit to 30 per minute
        ->withoutMiddleware('csrf');
    
    Route::post('payments/intent', [PaymentController::class, 'intent'])
        ->middleware('throttle:60,1'); // Rate limit to 60 per minute
    
    Route::get('payments/intents/{uuid}', [PaymentController::class, 'showIntent']);

    Route::prefix('admin/payments')->middleware('auth:sanctum')->group(function () {
        Route::get('/transactions', [AdminPaymentController::class, 'transactions'])
            ->middleware('permission:payment.view');
        Route::post('/orders/{orderUuid}/refund', [AdminPaymentController::class, 'refund'])
            ->middleware('permission:payment.refund');
    });
});
