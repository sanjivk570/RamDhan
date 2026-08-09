<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Media\Controllers\MediaController;

/**
 * Media module API routes.
 *
 * Defines API endpoints for user management including
 * listing, creating, updating, and deleting users.
 * Routes are protected using Sanctum authentication
 * and permission-based authorization.
 *
 * @package App\Modules\Media\Routes
 * @author Sanjiv Kumar Kushwaha
 */
Route::prefix('api/v1')->group(function () {
    Route::middleware("auth:sanctum")->prefix("media")->group(function () {
        Route::get("/", [MediaController::class, "index"]);

        Route::post("/", [MediaController::class, "store"]);

        Route::get("/{uuid}", [MediaController::class, "show"]);

        Route::put("/{uuid}", [MediaController::class, "update"]);

        Route::delete("/{uuid}", [MediaController::class, "destroy"]);

        Route::post("/{uuid}/restore", [MediaController::class, "restore"]);

        Route::delete("/{uuid}/force", [MediaController::class, "forceDelete"]);

        Route::patch("/{uuid}/primary", [MediaController::class, "setPrimary"]);
    });
});

