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
    Route::get('users', [UserController::class, 'index']); // Use GET for listing users
    Route::post('users/show', [UserController::class, 'show']); // Use POST for showing a user
    Route::post('users/store', [UserController::class, 'store']); // Use POST for creating user
    Route::post('users/update', [UserController::class, 'update']); // Use POST for updating user
    Route::post('users/destroy', [UserController::class, 'destroy']); // Use POST for deleting user

    // Permission Routes
    Route::get('permissions', [PermissionController::class, 'index']); // Use GET for listing permissions
    Route::post('permissions/show', [PermissionController::class, 'show']); // Use POST for showing a permission
    Route::post('permissions/store', [PermissionController::class, 'store']); // Use POST for creating permission
    Route::post('permissions/update', [PermissionController::class, 'update']); // Use POST for updating permission
    Route::post('permissions/destroy', [PermissionController::class, 'destroy']); // Use POST for deleting permission

    // Role Routes
    Route::get('roles', [RoleController::class, 'index']); // Use GET for listing roles
    Route::post('roles/show', [RoleController::class, 'show']); // Use POST for showing a role
    Route::post('roles/store', [RoleController::class, 'store']); // Use POST for creating role
    Route::post('roles/update', [RoleController::class, 'update']); // Use POST for updating role
    Route::post('roles/destroy', [RoleController::class, 'destroy']); // Use POST for deleting role

    // Article Routes
    Route::get('articles', [ArticleController::class, 'index']); // Use GET for listing articles
    Route::post('articles/show', [ArticleController::class, 'show']); // Use POST for showing an article
    Route::post('articles/store', [ArticleController::class, 'store']); // Use POST for creating article
    Route::post('articles/update', [ArticleController::class, 'update']); // Use POST for updating article
    Route::post('articles/destroy', [ArticleController::class, 'destroy']); // Use POST for deleting article

    // Using POST and GET methods for relationships, removing sensitive data from the URL
    Route::post('users/roles', [UserController::class, 'roles'])->name('users.roles');
    Route::post('roles/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('roles/remove-permission', [RoleController::class, 'removePermission'])->name('roles.removePermission');
    Route::post('roles/assign-permission', [RoleController::class, 'assignPermission'])->name('roles.assignPermission');
});
