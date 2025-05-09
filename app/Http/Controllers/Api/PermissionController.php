<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View_Permissions', only: ['index', 'show']),
            new Middleware('permission:Edit_Permissions', only: ['edit', 'update']),
            new Middleware('permission:Create_Permissions', only: ['store']),
            new Middleware('permission:Delete_Permissions', only: ['destroy']),
        ];
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
            'name' => 'required|unique:permissions|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $permission = Permission::create(['name' => $request->name]);

        return response()->json([
            'status' => true,
            'message' => 'Permission created successfully.',
            'data' => $permission
        ], 201);
    }

    // Show (POST, avoid sensitive data in URL)
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

    // Update (POST, avoid sensitive data in URL)
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
            'name' => 'required|min:3|unique:permissions,name,' . $request->id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $permission->name = $request->name;
        $permission->save();

        return response()->json([
            'status' => true,
            'message' => 'Permission updated successfully.',
            'data' => $permission
        ]);
    }

    // Destroy (POST, avoid sensitive data in URL)
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
