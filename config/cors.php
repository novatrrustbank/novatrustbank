<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        '/login',
        '/logout',
        '/session-test',
    ],

    'allowed_methods' => ['*'],

    // Allow your exact domain (not "*") so cookies work
    'allowed_origins' => [
        'https://novatrustbank.onrender.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // MUST be true for cookies to work
    'supports_credentials' => true,

];
