<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class UserController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return[
            new Middleware('permission:View_Users', only: ['index', 'show', 'roles']),
            new Middleware('permission:Edit_Users', only: ['update']),
        ];
    }
    
    // Use GET for listing users
    public function index(Request $request)
    {
        $users = User::latest()->paginate(10);
        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }

    // Use GET for showing a user, no sensitive data in the URL
    public function show(Request $request)
    {
        $id = $request->input('id');
        $user = User::findOrFail($id);
        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }

    // Use POST for updating a user, no sensitive data in the URL
    public function update(Request $request)
    {
        $id = $request->input('id');
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Sync roles if provided
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully.',
            'user' => $user
        ]);
    }

    // Use POST for deleting a user, no sensitive data in the URL
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

    // Use GET for showing roles, no sensitive data in the URL
    public function roles(Request $request)
    {
        $id = $request->input('id');
        $user = User::findOrFail($id);

        return response()->json([
            'status' => true,
            'user_id' => $user->id,
            'name' => $user->name,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name') // ← include role permissions too
        ]);
    }
}
