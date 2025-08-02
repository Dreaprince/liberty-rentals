<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Registers a new user account and returns the user details.
     *
     * @bodyParam name string required The user's name. Example: John Doe
     * @bodyParam email string required A valid email address. Example: john@example.com
     * @bodyParam password string required Minimum 6 characters. Example: password123
     * @bodyParam role string Optional. Allowed values: "admin", "user". Default is "user".
     *
     * @response 201 {
     *   "message": "User registered successfully",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "user",
     *     "created_at": "2025-08-02T12:00:00.000000Z",
     *     "updated_at": "2025-08-02T12:00:00.000000Z"
     *   }
     * }
     */
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'in:user,admin'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $fields['role'] ?? 'user',
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Log in an existing user
     *
     * Returns a token and user details if credentials are correct.
     *
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The user's password. Example: password123
     *
     * @response 200 {
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
     *   "token_type": "Bearer",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "user",
     *     "created_at": "2025-08-02T12:00:00.000000Z",
     *     "updated_at": "2025-08-02T12:00:00.000000Z"
     *   }
     * }
     *
     * @response 401 {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (! $user || ! Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}
