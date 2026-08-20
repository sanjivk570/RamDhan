<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Cart\Controllers\CartController;
use App\Modules\Cart\Controllers\Admin\CartController as AdminCartController;

Route::prefix('api/v1')->group(function () {
    Route::get('cart', [CartController::class, 'show'])
        ->middleware('throttle:100,1'); // Rate limit to 100 per minute
    
    Route::post('cart/items', [CartController::class, 'add'])
        ->middleware('throttle:100,1'); // Rate limit to 100 per minute
    
    Route::put('cart/items/{uuid}', [CartController::class, 'update'])
        ->middleware('throttle:100,1'); // Rate limit to 100 per minute
    
    Route::delete('cart/items/{uuid}', [CartController::class, 'remove'])
        ->middleware('throttle:100,1'); // Rate limit to 100 per minute
    
    Route::middleware('auth:customer')->post('cart/merge', [CartController::class, 'merge'])
        ->middleware('throttle:20,1'); // Rate limit to 20 per minute

    Route::prefix('admin/carts')->middleware(['auth:sanctum', 'permission:cart.view'])->group(function () {
        Route::get('/', [AdminCartController::class, 'index']);
        Route::get('/{uuid}', [AdminCartController::class, 'show']);
    });
});
