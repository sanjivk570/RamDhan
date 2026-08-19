<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Wishlist\Controllers\WishlistController;
use App\Modules\Wishlist\Controllers\Admin\WishlistController as AdminWishlistController;

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:customer')->group(function () {
        Route::get('wishlist', [WishlistController::class, 'index']);
        Route::post('wishlist', [WishlistController::class, 'store']);
        Route::delete('wishlist/{uuid}', [WishlistController::class, 'destroy']);
    });

    Route::prefix('admin/wishlists')->middleware(['auth:sanctum', 'permission:wishlist.view'])
        ->get('/', [AdminWishlistController::class, 'index']);
});
