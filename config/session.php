<?php

declare(strict_types=1);

use Phenix\Util\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "local", "redis"
    |
    */

    'driver' => env('SESSION_DRIVER', fn (): string => 'redis'),

    'lifetime' => env('SESSION_LIFETIME', fn () => 120),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection
    |--------------------------------------------------------------------------
    |
    | For "redis" session drivers, you may specify a specific connection that
    | should be used to manage the sessions. This should correspond to a
    | connection in your database configuration options.
    */

    'connection' => env('SESSION_CONNECTION', fn () => 'default'),

    'cookie_name' => env(
        'SESSION_COOKIE_NAME',
        fn () => Str::slug(env('APP_NAME', fn () => 'phenix'), '_') . '_session'
    ),

    'path' => '/',

    'domain' => env('SESSION_DOMAIN'),

    'secure' => env('SESSION_SECURE_COOKIE'),

    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | Supported: "Lax", "Strict", "None"
    |
    */

    'same_site' => 'Lax',

];
