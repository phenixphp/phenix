<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', static fn (): string => 'Phenix'),
    'env' => env('APP_ENV', static fn (): string => 'local'),
    'url' => env('APP_URL', static fn (): string => 'http://127.0.0.1'),
    'port' => env('APP_PORT', static fn (): int => 1337),
    'cert_path' => env('APP_CERT_PATH', static fn (): string|null => null),
    'key' => env('APP_KEY'),
    'previous_key' => env('APP_PREVIOUS_KEY'),

    /*
    |--------------------------------------------------------------------------
    | App mode
    |--------------------------------------------------------------------------
    | Controls how the HTTP server determines client connection details.
    |
    | direct:
    |   The server is exposed directly to clients. Remote address, scheme,
    |   and host are taken from the TCP connection and request line.
    |
    | proxied:
    |   The server runs behind a reverse proxy or load balancer (e.g., Nginx,
    |   HAProxy, AWS ALB). Client information is derived from standard
    |   forwarding headers only when the request comes from a trusted proxy.
    |   Configure trusted proxies in `trusted_proxies` (IP addresses or CIDRs).
    |   When enabled, the server will honor `Forwarded`, `X-Forwarded-For`,
    |   `X-Forwarded-Proto`, and `X-Forwarded-Host` headers from trusted
    |   sources, matching Amphp's behind-proxy behavior.
    |
    | Supported values: "direct", "proxied"
    |
    */

    'app_mode' => env('APP_MODE', static fn (): string => 'direct'),
    'trusted_proxies' => env('APP_TRUSTED_PROXIES', static fn (): array => []),

    /*
    |--------------------------------------------------------------------------
    | Server runtime mode
    |--------------------------------------------------------------------------
    | Controls whether the HTTP server runs as a single process (default) or
    | under amphp/cluster.
    |
    | Supported values:
    |   - "single"  (single process)
    |   - "cluster" (run with vendor/bin/cluster and cluster sockets)
    |
    */
    'server_mode' => env('APP_SERVER_MODE', static fn (): string => 'single'),
    'debug' => env('APP_DEBUG', static fn (): bool => true),
    'locale' => 'en',
    'fallback_locale' => 'en',
    'middlewares' => [
        'global' => [
            \Phenix\Http\Middlewares\HandleCors::class,
            \Phenix\Cache\RateLimit\Middlewares\RateLimiter::class,
            \Phenix\Auth\Middlewares\TokenRateLimit::class,
        ],
        'router' => [
            \Phenix\Http\Middlewares\ResponseHeaders::class,
        ],
    ],
    'providers' => [
        \Phenix\Filesystem\FilesystemServiceProvider::class,
        \Phenix\Console\CommandsServiceProvider::class,
        \Phenix\Routing\RouteServiceProvider::class,
        \Phenix\Database\DatabaseServiceProvider::class,
        \Phenix\Redis\RedisServiceProvider::class,
        \Phenix\Auth\AuthServiceProvider::class,
        \Phenix\Tasks\TaskServiceProvider::class,
        \Phenix\Views\ViewServiceProvider::class,
        \Phenix\Cache\CacheServiceProvider::class,
        \Phenix\Mail\MailServiceProvider::class,
        \Phenix\Crypto\CryptoServiceProvider::class,
        \Phenix\Queue\QueueServiceProvider::class,
        \Phenix\Events\EventServiceProvider::class,
        \Phenix\Translation\TranslationServiceProvider::class,
        \Phenix\Scheduling\SchedulingServiceProvider::class,
        \Phenix\Validation\ValidationServiceProvider::class,
    ],
    'response' => [
        'headers' => [
            \Phenix\Http\Headers\XDnsPrefetchControl::class,
            \Phenix\Http\Headers\XFrameOptions::class,
            \Phenix\Http\Headers\StrictTransportSecurity::class,
            \Phenix\Http\Headers\XContentTypeOptions::class,
            \Phenix\Http\Headers\ReferrerPolicy::class,
            \Phenix\Http\Headers\CrossOriginResourcePolicy::class,
            \Phenix\Http\Headers\CrossOriginOpenerPolicy::class,
        ],
    ],
];
