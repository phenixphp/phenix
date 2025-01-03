<?php

declare(strict_types=1);

use Phenix\Database\Constants\Driver;

return [
    'default' => env('DB_CONNECTION', fn () => 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => Driver::MYSQL,
            'host' => env('DB_HOST', fn () => '127.0.0.1'),
            'port' => env('DB_PORT', fn () => '3306'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'unix_socket' => env('DB_SOCKET'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ],
        'postgresql' => [
            'driver' => Driver::POSTGRESQL,
            'host' => env('DB_HOST', fn () => '127.0.0.1'),
            'port' => env('DB_PORT', fn () => '3306'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ],
    ],

    'redis' => [
        'connections' => [
            'default' => [
                'scheme' => env('REDIS_SCHEME', fn () => 'redis'),
                'host' => env('REDIS_HOST', fn () => '127.0.0.1'),
                'username' => env('REDIS_USERNAME'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', fn () => '6379'),
                'database' => env('REDIS_DB', fn () => 0),
            ],
        ],
    ],

    'paths' => [
        'migrations' => base_path('database' . DIRECTORY_SEPARATOR . 'migrations'),
        'seeds' => base_path('database' . DIRECTORY_SEPARATOR . 'seeds'),
    ],
];
