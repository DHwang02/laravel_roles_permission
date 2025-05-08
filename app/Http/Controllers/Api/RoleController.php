<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return[
            new Middleware('permission:View_Roles',only:['index','show','permissions']),
            new Middleware('permission:Edit_Roles',only:['edit','update','assignPermission','removePermission']),
            new Middleware('permission:Create_Roles',only:['store']),
            new Middleware('permission:Delete_Roles',only:['destroy']),
        ];
    }
    public function index()
    {
        $roles = Role::orderBy('name', 'ASC')->paginate(25);
        return response()->json([
            'status' => true,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return response()->json([
            'status' => true,
            'message' => 'Role added successfully.',
            'role' => $role
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();

        return response()->json([
            'status' => true,
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id . '|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $role->name = $request->name;
        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully.',
            'role' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully.'
        ]);
    }

    public function permissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $role = Role::findOrFail($id);

        if ($role->hasPermissionTo($request->permission)) {
            return response()->json([
                'status' => false,
                'message' => "Permission '{$request->permission}' is already assigned to role '{$role->name}'."
            ], 409); // 409 Conflict
        }

        $role->givePermissionTo($request->permission);

        return response()->json([
            'status' => true,
            'message' => "Permission '{$request->permission}' added to role '{$role->name}'."
        ]);
    }

    public function removePermission(Request $request, $id)
    {
        $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $role = Role::findOrFail($id);

        if (! $role->hasPermissionTo($request->permission)) {
            return response()->json([
                'status' => false,
                'message' => "Permission '{$request->permission}' is not assigned to role '{$role->name}'."
            ], 404); // 404 Not Found
        }

        $role->revokePermissionTo($request->permission);

        return response()->json([
            'status' => true,
            'message' => "Permission '{$request->permission}' removed from role '{$role->name}'."
        ]);
    }

}
