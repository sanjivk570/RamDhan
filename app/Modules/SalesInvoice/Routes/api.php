<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\SalesInvoice\Controllers\SalesInvoiceController;
use App\Modules\SalesInvoice\Controllers\Admin\SalesInvoiceController as AdminInvoiceController;

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:customer')->group(function () {
        Route::get('invoices', [SalesInvoiceController::class, 'index']);
        Route::get('invoices/{uuid}', [SalesInvoiceController::class, 'show']);
    });

    Route::prefix('admin/invoices')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [AdminInvoiceController::class, 'index'])
            ->middleware('permission:invoice.view');
        Route::get('/{uuid}', [AdminInvoiceController::class, 'show'])
            ->middleware('permission:invoice.view');
        Route::post('/orders/{orderUuid}/generate', [AdminInvoiceController::class, 'generate'])
            ->middleware('permission:invoice.create');
    });
});
