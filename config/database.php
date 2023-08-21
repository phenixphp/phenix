<?php

declare(strict_types=1);

return [
    'default' => env('DB_CONNECTION', fn () => 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
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
            'driver' => 'postgresql',
            'host' => env('DB_HOST', fn () => '127.0.0.1'),
            'port' => env('DB_PORT', fn () => '3306'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ],
    ],
];
