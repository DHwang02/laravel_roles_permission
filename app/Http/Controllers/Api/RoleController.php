<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Http\Middlewares\AccessMiddleware;

class RoleController extends Controller
{
    public function __construct()
    {
        foreach (AccessMiddleware::permissions() as $rule) {
            $this->middleware($rule['middleware'])->only($rule['only']);
        }
    }

    // Index (GET)
    public function index()
    {
        $roles = Role::orderBy('name', 'ASC')->paginate(25);
        return response()->json([
            'status' => true,
            'roles' => $roles
        ]);
    }

    // Store (POST)
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

    // Show (POST, avoid sensitive data in URL)
    public function show(Request $request)
    {
        $role = Role::find($request->id);

        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        return response()->json([
            'status' => true,
            'role' => $role
        ]);
    }

    // Update (POST, avoid sensitive data in URL)
    public function update(Request $request)
    {
        $role = Role::find($request->id);

        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $request->id . '|min:3'
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

    // Destroy (POST, avoid sensitive data in URL)
    public function destroy(Request $request)
    {
        $role = Role::find($request->id);

        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully.'
        ]);
    }

    // Permissions (GET)
    public function permissions(Request $request)
    {
        $role = Role::with('permissions')->find($request->id);

        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    // Assign Permission (POST)
    public function assignPermission(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:roles,id',
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $role = Role::find($request->id);

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

    // Remove Permission (POST)
    public function removePermission(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:roles,id',
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $role = Role::find($request->id);

        if (!$role->hasPermissionTo($request->permission)) {
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
