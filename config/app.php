<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', static fn (): string => 'Phenix'),
    'env' => env('APP_ENV', static fn (): string => 'local'),
    'url' => env('APP_URL', static fn (): string => 'http://127.0.0.1'),
    'port' => env('APP_PORT', static fn (): int => 1337),
    'key' => env('APP_KEY'),
    'previous_key' => env('APP_PREVIOUS_KEY'),
    'debug' => env('APP_DEBUG', static fn (): bool => true),
    'middlewares' => [
        'global' => [
            \Phenix\Http\Middlewares\HandleCors::class,
        ],
        'router' => [],
    ],
    'providers' => [
        \Phenix\Console\CommandsServiceProvider::class,
        \Phenix\Routing\RouteServiceProvider::class,
        \Phenix\Database\DatabaseServiceProvider::class,
        \Phenix\Redis\RedisServiceProvider::class,
        \Phenix\Filesystem\FilesystemServiceProvider::class,
        \Phenix\Tasks\TaskServiceProvider::class,
        \Phenix\Views\ViewServiceProvider::class,
        \Phenix\Mail\MailServiceProvider::class,
        \Phenix\Crypto\CryptoServiceProvider::class,
        \Phenix\Queue\QueueServiceProvider::class,
    ],
];
