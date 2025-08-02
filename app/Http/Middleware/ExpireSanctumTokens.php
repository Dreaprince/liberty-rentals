<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpireSanctumTokens
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->expires_at && now()->greaterThan($token->expires_at)) {
            $token->delete();

            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired. Please login again.'
            ], 401);
        }

        return $next($request);
    }
}

