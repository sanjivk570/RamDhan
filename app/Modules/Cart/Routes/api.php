<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Cart\Controllers\CartController;
use App\Modules\Cart\Controllers\Admin\CartController as AdminCartController;

Route::prefix('api/v1')->group(function () {
    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart/items', [CartController::class, 'add']);
    Route::put('cart/items/{uuid}', [CartController::class, 'update']);
    Route::delete('cart/items/{uuid}', [CartController::class, 'remove']);
    Route::middleware('auth:customer')->post('cart/merge', [CartController::class, 'merge']);

    Route::prefix('admin/carts')->middleware(['auth:sanctum', 'permission:cart.view'])->group(function () {
        Route::get('/', [AdminCartController::class, 'index']);
        Route::get('/{uuid}', [AdminCartController::class, 'show']);
    });
});
