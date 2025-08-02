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
        // Step 1: Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'in:user,admin'
        ]);

        // Step 2: Check if user already exists
        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'A user with this email already exists.',
            ], 409); // 409 Conflict
        }

        // Step 3: Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'] ?? 'user',
        ]);

        // Step 4: Return formatted response
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user
            ]
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
        // Step 1: Validate input
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Step 2: Check if user exists
        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect Username/Password',
            ], 401);
        }

        // Step 3: Verify password
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect Username/Password',
            ], 401);
        }

        // Step 4: Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Step 5: Return successful response
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ], 200);
    }

}
