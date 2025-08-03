<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated requests.
     */
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'status' => 'error',
            'message' => 'Authentication token is missing or invalid.',
        ], 401));
    }

    /**
     * Override redirectTo() for web fallback (optional).
     */
    protected function redirectTo($request)
    {
        // Only used in web routes. Not necessary for API.
        return null;
    }
}

