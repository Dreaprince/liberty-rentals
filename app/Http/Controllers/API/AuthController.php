<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\NewAccessToken;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @bodyParam name string required Name of the user.
     * @bodyParam email string required Valid email address.
     * @bodyParam password string required Minimum of 6 characters.
     * @bodyParam role string Optional. Must be 'admin' or 'user'. Defaults to 'user'.
     *
     * @response 201 {
     *   "status": "success",
     *   "message": "User registered successfully",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "role": "user",
     *       ...
     *     }
     *   }
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'in:user,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'user',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => ['user' => $user],
        ], 201);
    }

    /**
     * Log in a user using Sanctum
     *
     * @bodyParam email string required The user's email.
     * @bodyParam password string required The user's password.
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Login successful",
     *   "data": {
     *     "access_token": "token_here",
     *     "token_type": "Bearer",
     *     "user": { ... }
     *   }
     * }
     *
     * @response 401 {
     *   "status": "error",
     *   "message": "Incorrect username or password"
     * }
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect username or password',
            ], 401);
        }

        // Create token with expiration
        /** @var NewAccessToken $tokenResult */
        $tokenResult = $user->createToken('auth_token');
        $accessToken = $tokenResult->accessToken;

        // Set expiry (e.g., 24 hours)
        $accessToken->expires_at = now()->addHours(24);
        $accessToken->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'access_token' => $tokenResult->plainTextToken,
                'expires_at' => $accessToken->expires_at,
                'user' => $user,
            ]
        ]);
    }

    /**
     * Logout (revoke current token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully'
        ]);
    }
}
