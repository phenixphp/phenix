<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', fn () => 'Phenix'),
    'env' => env('APP_ENV', fn () => 'local'),
    'url' => ['0.0.0.0', '[::]'],
    'port' => env('APP_PORT', fn () => 1337),
    'providers' => [
        Core\Providers\CoreServiceProvider::class,
        Core\Providers\DatabaseServiceProvider::class,
        Core\Providers\FilesystemServiceProvider::class,
    ],
];
