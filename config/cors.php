<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // This determines which endpoints allow CORS
    'paths' => ['api/*', 'docs/*', 'sanctum/csrf-cookie'],

    // Allowed HTTP methods
    'allowed_methods' => ['*'],

    // Allowed origins (Frontend domains)
    // 'allowed_origins' => ['*'], // Change to ['http://localhost:3000'] or your frontend domain in production
    //'allowed_origins' => ['http://localhost:3000', 'http://127.0.0.1:8000', '*'],
    'allowed_origins' => [
        '*', // allow all for development, including Postman Desktop
        'http://localhost:3000', // local frontend
        'http://127.0.0.1:3000', // alternative localhost
        'https://web.postman.co' // Postman Web (if needed)
    ],


    // If you need wildcard origin support
    'allowed_origins_patterns' => [],

    // Headers your frontend is allowed to send
    'allowed_headers' => ['*'],

    // Headers that will be exposed to the frontend
    'exposed_headers' => [],

    // Time in seconds that the browser can cache the preflight response
    'max_age' => 0,

    // Whether cookies are supported (only allow true if using credentials)
    'supports_credentials' => false,
];
