<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'docs', 'api/docs'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // Use ['http://localhost:8000'] in production

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
