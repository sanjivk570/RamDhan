<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Promotion\Controllers\CouponController;
use App\Modules\Promotion\Controllers\Admin\CouponController as AdminCouponController;

Route::prefix('api/v1')->group(function () {
    Route::post('cart/coupon', [CouponController::class, 'apply']);
    Route::delete('cart/coupon', [CouponController::class, 'remove']);

    Route::prefix('admin/coupons')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [AdminCouponController::class, 'index'])
            ->middleware('permission:coupon.view');
        Route::post('/', [AdminCouponController::class, 'store'])
            ->middleware('permission:coupon.create');
        Route::put('/{uuid}', [AdminCouponController::class, 'update'])
            ->middleware('permission:coupon.update');
        Route::delete('/{uuid}', [AdminCouponController::class, 'destroy'])
            ->middleware('permission:coupon.delete');
    });
});
