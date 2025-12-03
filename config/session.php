<?php

use Illuminate\Support\Str;

return [

    'driver' => env('SESSION_DRIVER', 'file'),

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    'encrypt' => env('SESSION_ENCRYPT', false),

    'files' => storage_path('framework/sessions'),

    'connection' => env('SESSION_CONNECTION', null),

    'table' => 'sessions',

    'store' => env('SESSION_STORE', null),

    'lottery' => [2, 100],

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),

    'path' => '/',

    /*
     * IMPORTANT: use a dot-prefixed domain in .env (SESSION_DOMAIN=novatrustbank.onrender.com)
     */
    'domain' => env('SESSION_DOMAIN', null),

    /*
     * Must be true on Render (HTTPS)
     */
    'secure' => env('SESSION_SECURE_COOKIE', true),

    /*
     * Keep JS out of the cookie
     */
    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
     * Use None for cross-site cookies (paired with secure=true)
     * Supported values: "lax", "strict", "none", null
     */
    'same_site' => env('SESSION_SAME_SITE', 'None'),

];
