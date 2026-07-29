<?php

/**
 * Authentication API Routes.
 *
 * This file defines all authentication-related API endpoints,
 * including user registration, login, logout, profile management,
 * password change, and password reset operations.
 *
 * @package App\Modules\Auth
 * @author Sanjiv Kumar Kushwaha
 */

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::prefix('api/v1/auth')->group(function () {

    // Public Routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});
