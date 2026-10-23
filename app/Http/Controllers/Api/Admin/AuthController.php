<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\UserRole;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Invalid credentials. Please check your email and password.', 401);
        }

        if ($user->role !== UserRole::ADMIN) {
            return ApiResponse::error('Unauthorized. Admin access only.', 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('admin-token')->plainTextToken;

        return ApiResponse::success([
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'avatar' => $user->avatar,
            ]
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully.');
    }

    public function profile(Request $request)
    {
        return ApiResponse::success([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role->value,
                'avatar' => $request->user()->avatar,
            ]
        ], 'Profile retrieved successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return ApiResponse::error('Current password is incorrect.', 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return ApiResponse::success(null, 'Password changed successfully.');
    }
}