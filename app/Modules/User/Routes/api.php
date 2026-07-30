<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;

/**
 * User module API routes.
 *
 * Defines API endpoints for user management including
 * listing, creating, updating, and deleting users.
 * Routes are protected using Sanctum authentication
 * and permission-based authorization.
 *
 * @package App\Modules\User\Routes
 * @author Sanjiv Kumar Kushwaha
 */
Route::prefix('api/v1')->group(function () {
    // Route::middleware(['auth:sanctum'])->group(function () {
    //     Route::get('users', [UserController::class, 'index']);
    //     Route::get('users/{uuid}', [UserController::class, 'show']);
    //     Route::post('users', [UserController::class, 'store']);
    //     Route::put('users/{uuid}', [UserController::class, 'update']);
    //     Route::patch('/users/{uuid}/status', [UserController::class, 'changeStatus']);
    //     Route::delete('/users/{uuid}', [UserController::class, 'destroy']);
    //     Route::patch('/users/{uuid}/restore', [UserController::class, 'restore']);
    // });

    Route::middleware([
        'auth:sanctum',
        'permission:user.view'
    ])->get('/users', [UserController::class, 'index']);

    Route::middleware([
        'auth:sanctum',
        'permission:user.create'
    ])->post('/users', [UserController::class, 'store']);

    Route::middleware([
        'auth:sanctum',
        'permission:user.update'
    ])->put('/users/{uuid}', [UserController::class, 'update']);

    Route::middleware([
        'auth:sanctum',
        'permission:user.delete'
    ])->delete('/users/{uuid}', [UserController::class, 'destroy']);

});

