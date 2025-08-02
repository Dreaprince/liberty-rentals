<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | For more details: https://laravel.com/docs/12.x/routing#cors
    |
    */

    // Apply CORS to these paths
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
        'user',
    ],

    // HTTP methods allowed
    'allowed_methods' => ['*'],

    // Only allow from these origins (no '*' if using credentials)
    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost:8000',
        'https://web.postman.co', // Optional: Postman Web
    ],

    // Wildcard pattern origins (not used here)
    'allowed_origins_patterns' => [],

    // Allowed request headers from frontend
    'allowed_headers' => ['*'],

    // Headers exposed back to the frontend
    'exposed_headers' => [],

    // Cache the preflight response for this many seconds
    'max_age' => 0,

    // Allow cookies/auth credentials
    'supports_credentials' => true,
];
