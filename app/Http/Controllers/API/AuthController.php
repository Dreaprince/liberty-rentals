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
     * @bodyParam name string required The user's name.
     * @bodyParam email string required Must be a valid email.
     * @bodyParam password string required Minimum 6 characters.
     * @bodyParam role string Optional: 'admin' or 'user'. Default is 'user'.
     * 
     * @response 201 {
     *   "message": "User registered successfully",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "user"
     *   }
     * }
     */
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'in:user,admin' // Optional: if provided, must be 'user' or 'admin'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $fields['role'] ?? 'user', // default to 'user' if not specified
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Log in an existing user
     *
     * @bodyParam email string required The user's email.
     * @bodyParam password string required The user's password.
     * 
     * @response 200 {
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
     *   "token_type": "Bearer",
     *   "user": { "id": 1, "name": "John", "email": "john@example.com" }
     * }
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
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


