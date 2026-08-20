<?php

// use Illuminate\Support\Facades\Route;
// use Modules\Customer\Controllers\CustomerController;

// Route::middleware(["auth:sanctum"])
//     ->prefix("/api/v1/customers")
//     ->group(function () {
//         Route::get("/", [CustomerController::class, "index"]);

//         Route::post("/", [CustomerController::class, "store"]);

//         Route::get("/{uuid}", [CustomerController::class, "show"]);

//         Route::put("/{uuid}", [CustomerController::class, "update"]);

//         Route::delete("/{uuid}", [CustomerController::class, "destroy"]);

//         Route::patch("/{uuid}/status", [ CustomerController::class, "changeStatus",]);

//         Route::patch("/{uuid}/restore", [CustomerController::class, "restore"]);
//     });

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Customer\Controllers\CustomerController;
use App\Modules\Customer\Controllers\CustomerAuthController;
use App\Modules\Customer\Controllers\CustomerAddressController;

Route::prefix("api/v1")->group(function () {

    // Admin Customer Management
    Route::middleware(["auth:sanctum", "permission:customer.view"])->get("/customers", [CustomerController::class, "index"]);
    Route::middleware(["auth:sanctum", "permission:customer.view"])->get("/customers/{uuid}", [CustomerController::class, "show"]);
    Route::middleware(["auth:sanctum", "permission:customer.create"])->post("/customers", [CustomerController::class, "store"]);
    Route::middleware(["auth:sanctum", "permission:customer.update"])->put("/customers/{uuid}", [CustomerController::class, "update"]);
    Route::middleware(["auth:sanctum", "permission:customer.update"])->patch("/customers/{uuid}/status", [CustomerController::class, "changeStatus"]);
    Route::middleware(["auth:sanctum", "permission:customer.delete"])->delete("/customers/{uuid}",[CustomerController::class, "destroy"]);
    Route::middleware(["auth:sanctum", "permission:customer.delete"])->patch("/customers/{uuid}/restore", [CustomerController::class, "restore"]);
    
    Route::prefix('/customer')->group(function () {
        Route::prefix('auth')->group(function () {
            //Public
            Route::post('/register', [CustomerAuthController::class, 'register']);
            Route::post('/login', [CustomerAuthController::class, 'login']);
            Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword']);
            Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword']);

            //Customer Authenticated
            Route::middleware('auth:customer')->group(function () {
                Route::post('/logout', [CustomerAuthController::class, 'logout']);
                Route::get('/profile', [CustomerAuthController::class, 'profile']);
                Route::put('/profile', [CustomerController::class, 'updateProfile']);
                Route::post('/change-password', [CustomerAuthController::class, 'changePassword']);
            });
        });
        Route::middleware(['auth:customer'])->group(function () {
            //Customer addresses
            Route::get('/addresses', [CustomerAddressController::class, 'index']);
            Route::get('/addresses/{uuid}', [CustomerAddressController::class, 'show']);
            Route::post('/addresses', [CustomerAddressController::class, 'store']);
            Route::put('/addresses/{uuid}', [CustomerAddressController::class, 'update']);
            Route::delete('/addresses/{uuid}', [CustomerAddressController::class, 'destroy']);
            Route::patch('/addresses/{uuid}/default', [CustomerAddressController::class, 'setDefault']);
        });
    });

});

