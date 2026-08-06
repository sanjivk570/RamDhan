<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Role\Controllers\RoleController;
use App\Modules\Role\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Role Module API Routes
|--------------------------------------------------------------------------
|
| Register all Role and Permission API routes.
| These routes are protected by Sanctum authentication and
| permission-based authorization.
|
| @package App\Modules\Role\Routes
| @author Sanjiv Kumar Kushwaha
|
*/
Route::middleware(['auth:sanctum'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Role APIs
    |--------------------------------------------------------------------------
    |
    | Endpoints for managing roles and synchronizing
    | role permissions.
    |
    */

    Route::prefix('api/v1/roles')->group(function () {

        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:role.view');

        Route::get('/{id}', [RoleController::class, 'show'])
            ->middleware('permission:role.view');

        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:role.create');

        Route::put('/{id}', [RoleController::class, 'update'])
            ->middleware('permission:role.update');

        Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->middleware('permission:role.delete');

        Route::get('/{id}/permissions', [RoleController::class, 'permissions'])
            ->middleware('permission:role.view');

        Route::put('/{id}/permissions', [RoleController::class, 'syncPermissions'])
            ->middleware('permission:role.update');

    });

    /*
    |--------------------------------------------------------------------------
    | Permission APIs
    |--------------------------------------------------------------------------
    |
    | Endpoints for viewing available permissions.
    |
    */

    Route::prefix('api/v1/permissions')->group(function () {

        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('permission:role.view');

        Route::get('/{uuid}', [PermissionController::class, 'show'])
            ->middleware('permission:role.view');

    });

});