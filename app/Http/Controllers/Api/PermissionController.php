<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use App\Http\Middlewares\AccessMiddleware;
use Illuminate\Support\Str;

class PermissionController extends Controller
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
        $permissions = Permission::orderBy('created_at', 'DESC')->paginate(25);
        return response()->json([
            'status' => true,
            'data' => $permissions
        ]);
    }

    // Store (POST)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $displayName = $request->display_name;
        $name = Str::slug($displayName, '-'); // Converts "Edit Permission" to "edit-permission"

        // Check for uniqueness of generated 'name'
        if (Permission::where('name', $name)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Permission name already exists.'
            ], 409);
        }

        $permission = Permission::create([
            'name' => $name,
            'display_name' => $displayName,
            'guard_name' => 'web'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Permission created successfully.',
            'data' => $permission
        ], 201);
    }

    // Show (POST)
    public function show(Request $request)
    {
        $permission = Permission::find($request->id);

        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Permission not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $permission
        ]);
    }

    // Update (POST)
    public function update(Request $request)
    {
        $permission = Permission::find($request->id);

        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Permission not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $displayName = $request->display_name;
        $name = Str::slug($displayName, '-');

        // Check if new name is already taken by another permission
        if (Permission::where('name', $name)->where('id', '!=', $permission->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Permission name already exists.'
            ], 409);
        }

        $permission->name = $name;
        $permission->display_name = $displayName;
        $permission->save();

        return response()->json([
            'status' => true,
            'message' => 'Permission updated successfully.',
            'data' => $permission
        ]);
    }

    // Destroy (POST)
    public function destroy(Request $request)
    {
        $permission = Permission::find($request->id);

        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Permission not found'
            ], 404);
        }

        $permission->delete();

        return response()->json([
            'status' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
