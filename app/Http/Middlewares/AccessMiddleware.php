<?php

namespace App\Http\Middlewares;

class AccessMiddleware
{
    public static function permissions(): array
    {
        return [
            ['middleware' => 'permission:view-permissions', 'only' => ['index', 'show']],
            ['middleware' => 'permission:edit-permissions', 'only' => ['edit', 'update']],
            ['middleware' => 'permission:create-permissions', 'only' => ['store']],
            ['middleware' => 'permission:delete-permissions', 'only' => ['destroy']],
        ];
    }

    public static function roles(): array
    {
        return [
            ['middleware' => 'permission:view-roles', 'only' => ['index', 'show', 'permissions']],
            ['middleware' => 'permission:edit-roles', 'only' => ['edit', 'update', 'assignPermission', 'removePermission']],
            ['middleware' => 'permission:create-roles', 'only' => ['store']],
            ['middleware' => 'permission:delete-roles', 'only' => ['destroy']],
        ];
    }

    public static function users(): array
    {
        return [
            ['middleware' => 'permission:view-users', 'only' => ['index', 'show', 'roles']],
            ['middleware' => 'permission:edit-users', 'only' => ['update']],
        ];
    }

    public static function articles(): array
    {
        return [
            ['middleware' => 'permission:view-articles', 'only' => ['index', 'show']],
            ['middleware' => 'permission:edit-articles', 'only' => ['update']],
            ['middleware' => 'permission:create-articles', 'only' => ['store']],
            ['middleware' => 'permission:delete-articles', 'only' => ['destroy']],
        ];
    }
}
