<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Modules\Shipping\Controllers\ShippingController;
use App\Modules\Shipping\Controllers\Admin\ShippingZoneController;
use App\Modules\Shipping\Controllers\Admin\ShippingMethodController;
use App\Modules\Shipping\Controllers\Admin\ShippingRateController;

Route::prefix("api/v1")->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Customer / Frontend
    |--------------------------------------------------------------------------
    */

    Route::middleware(["auth:customer"])->group(function () {
        Route::post("/shipping/rates", [ShippingController::class, "rates"]);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    Route::prefix("admin")
        ->middleware(["auth:sanctum"])
        ->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Shipping Zones
            |--------------------------------------------------------------------------
            */

            Route::get("/shipping/zones", [
                ShippingZoneController::class,
                "index",
            ]);

            Route::post("/shipping/zones", [
                ShippingZoneController::class,
                "store",
            ]);

            Route::put("/shipping/zones/{uuid}", [
                ShippingZoneController::class,
                "update",
            ]);

            Route::delete("/shipping/zones/{uuid}", [
                ShippingZoneController::class,
                "destroy",
            ]);

            /*
            |--------------------------------------------------------------------------
            | Shipping Methods
            |--------------------------------------------------------------------------
            */

            Route::get("/shipping/methods", [
                ShippingMethodController::class,
                "index",
            ]);

            Route::post("/shipping/methods", [
                ShippingMethodController::class,
                "store",
            ]);

            Route::put("/shipping/methods/{uuid}", [
                ShippingMethodController::class,
                "update",
            ]);

            Route::delete("/shipping/methods/{uuid}", [
                ShippingMethodController::class,
                "destroy",
            ]);

            /*
            |--------------------------------------------------------------------------
            | Shipping Rates
            |--------------------------------------------------------------------------
            */

            Route::get("/shipping/rates", [
                ShippingRateController::class,
                "index",
            ]);

            Route::post("/shipping/rates", [
                ShippingRateController::class,
                "store",
            ]);

            Route::put("/shipping/rates/{uuid}", [
                ShippingRateController::class,
                "update",
            ]);

            Route::delete("/shipping/rates/{uuid}", [
                ShippingRateController::class,
                "destroy",
            ]);
        });
});
