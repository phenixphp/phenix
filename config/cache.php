<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    | Supported: "local", "file", "redis"
    |
    */

    'default' => env('CACHE_STORE', static fn (): string => 'local'),

    'stores' => [
        'local' => [
            'size_limit' => 1024,
            'gc_interval' => 5,
        ],

        'file' => [
            'path' => base_path('storage/framework/cache'),
        ],

        'redis' => [
            'connection' => env('CACHE_REDIS_CONNECTION', static fn (): string => 'default'),
        ],
    ],

    'prefix' => env('CACHE_PREFIX', static fn (): string => 'phenix_cache_'),

    /*
    |--------------------------------------------------------------------------
    | Default Cache TTL Minutes
    |--------------------------------------------------------------------------
    |
    | This option controls the default time-to-live (TTL) in minutes for cache
    | items. It is used as the default expiration time for all cache stores
    | unless a specific TTL is provided when setting a cache item.
    */
    'ttl' => env('CACHE_TTL', static fn (): int => 60),

    'rate_limit' => [
        'enabled' => env('RATE_LIMIT_ENABLED', static fn (): bool => true),
        'store' => env('RATE_LIMIT_STORE', static fn (): string => 'local'),
        'per_minute' => env('RATE_LIMIT_PER_MINUTE', static fn (): int => 60),
        'connection' => env('RATE_LIMIT_REDIS_CONNECTION', static fn (): string => 'default'),
    ],
];
