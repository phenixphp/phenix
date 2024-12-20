<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', fn () => 'Phenix'),
    'env' => env('APP_ENV', fn () => 'local'),
    'url' => env('APP_URL', fn () => '0.0.0.0'),
    'port' => env('APP_PORT', fn () => 1337),
    'middlewares' => [
        'global' => [
            \App\Http\Middleware\HandleCors::class,
        ],
        'router' => [],
    ],
    'providers' => [
        Phenix\Providers\CommandsServiceProvider::class,
        Phenix\Providers\RouteServiceProvider::class,
        Phenix\Providers\DatabaseServiceProvider::class,
        Phenix\Providers\FilesystemServiceProvider::class,
    ],
];
