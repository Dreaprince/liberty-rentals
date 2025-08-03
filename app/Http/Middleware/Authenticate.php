<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        // For API: send JSON response instead of redirecting to 'login' route
        if (!$request->expectsJson()) {
            abort(response()->json([
                'message' => 'Unauthenticated. No token provided or token is invalid.'
            ], 401));
        }

        return null;
    }
}
