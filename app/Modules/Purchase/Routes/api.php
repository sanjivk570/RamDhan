<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use App\Modules\Purchase\Controllers\PurchaseOrderController;
use App\Modules\Purchase\Controllers\GoodsReceiptController;
use App\Modules\Purchase\Controllers\PurchaseInvoiceController;
use App\Modules\Purchase\Controllers\PurchasePaymentController;
use App\Modules\Purchase\Controllers\PurchaseReturnController;

Route::prefix("api/v1")
    ->middleware("auth:sanctum")
    ->group(function (): void {
        Route::get("purchase-orders", [
            PurchaseOrderController::class,
            "index",
        ])->middleware("permission:purchase.view");
        Route::get("purchase-orders/{uuid}", [
            PurchaseOrderController::class,
            "show",
        ])->middleware("permission:purchase.view");
        Route::post("purchase-orders", [
            PurchaseOrderController::class,
            "store",
        ])->middleware("permission:purchase.create");
        Route::put("purchase-orders/{uuid}", [
            PurchaseOrderController::class,
            "update",
        ])->middleware("permission:purchase.update");
        Route::patch("purchase-orders/{uuid}/submit", [
            PurchaseOrderController::class,
            "submit",
        ])->middleware("permission:purchase.submit");
        Route::patch("purchase-orders/{uuid}/approve", [
            PurchaseOrderController::class,
            "approve",
        ])->middleware("permission:purchase.approve");
        Route::patch("purchase-orders/{uuid}/cancel", [
            PurchaseOrderController::class,
            "cancel",
        ])->middleware("permission:purchase.cancel");
        Route::get("goods-receipts", [
            GoodsReceiptController::class,
            "index",
        ])->middleware("permission:purchase.grn.view");
        Route::get("goods-receipts/{uuid}", [
            GoodsReceiptController::class,
            "show",
        ])->middleware("permission:purchase.grn.view");
        Route::post("goods-receipts", [
            GoodsReceiptController::class,
            "store",
        ])->middleware("permission:purchase.grn.create");
        Route::patch("goods-receipts/{uuid}/post", [
            GoodsReceiptController::class,
            "post",
        ])->middleware("permission:purchase.grn.post");
        Route::patch("goods-receipts/{uuid}/void", [
            GoodsReceiptController::class,
            "void",
        ])->middleware("permission:purchase.grn.void");

        Route::get("purchase-invoices", [
            PurchaseInvoiceController::class,
            "index",
        ])->middleware("permission:purchase.invoice.view");
        Route::get("purchase-invoices/{uuid}", [
            PurchaseInvoiceController::class,
            "show",
        ])->middleware("permission:purchase.invoice.view");
        Route::post("purchase-invoices", [
            PurchaseInvoiceController::class,
            "store",
        ])->middleware("permission:purchase.invoice.create");
        Route::patch("purchase-invoices/{uuid}/post", [
            PurchaseInvoiceController::class,
            "post",
        ])->middleware("permission:purchase.invoice.post");
        Route::get("purchase-payments", [
            PurchasePaymentController::class,
            "index",
        ])->middleware("permission:purchase.payment.view");
        Route::get("purchase-payments/{uuid}", [
            PurchasePaymentController::class,
            "show",
        ])->middleware("permission:purchase.payment.view");
        Route::post("purchase-payments", [
            PurchasePaymentController::class,
            "store",
        ])->middleware("permission:purchase.payment.create");
        Route::get("purchase-returns", [
            PurchaseReturnController::class,
            "index",
        ])->middleware("permission:purchase.return.view");
        Route::get("purchase-returns/{uuid}", [
            PurchaseReturnController::class,
            "show",
        ])->middleware("permission:purchase.return.view");
        Route::post("purchase-returns", [
            PurchaseReturnController::class,
            "store",
        ])->middleware("permission:purchase.return.create");
        Route::patch("purchase-returns/{uuid}/post", [
            PurchaseReturnController::class,
            "post",
        ])->middleware("permission:purchase.return.post");
    });
