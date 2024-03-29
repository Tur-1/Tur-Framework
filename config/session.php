<?php

return [

    'lifetime' => env('SESSION_LIFETIME', 3600),

    'expire_on_close' => false,

    'name' => strtolower(str_replace('', '_', env('APP_NAME', 'TurFramework') . '_session')),

    'path' => '/',

    'files' => storage_path('framework/sessions'),

    'domain' => env('SESSION_DOMAIN', 'localhost'),

    'secure' => env('SESSION_SECURE_COOKIE', true),

    'http_only' => true,

    'samesite' => 'lax',
];
