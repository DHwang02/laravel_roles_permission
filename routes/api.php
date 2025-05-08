<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\UserController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('refresh-token', [AuthController::class, 'refreshToken'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::resource('users', UserController::class);

    // Permission Routes
    Route::resource('permissions', PermissionController::class);

    // Role Routes
    Route::resource('roles', RoleController::class);

    // Article Routes
    Route::resource('articles', ArticleController::class);

    Route::get('/users/{id}/roles', [UserController::class, 'roles']);
    Route::get('/roles/{id}/permissions', [RoleController::class, 'permissions']);
    Route::delete('/roles/{id}/permissions', [RoleController::class, 'removePermission']);
    Route::post('/roles/{id}/permissions', [RoleController::class, 'assignPermission']);
});





