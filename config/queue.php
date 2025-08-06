<?php

return [
    'default' => env('QUEUE_DRIVER', fn (): string => 'database'),

    'drivers' => [
        'parallel' => [
            'timeout' => env('PARALLEL_QUEUE_TIMEOUT', fn (): int => 2),
            'chunk_size' => env('PARALLEL_QUEUE_CHUNK_SIZE', fn (): int => 10),
        ],

        'database' => [
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', fn (): string => 'tasks'),
            'queue' => env('DB_QUEUE', fn (): string => 'default'),
        ],

        'redis' => [
            'connection' => env('REDIS_QUEUE_CONNECTION', fn (): string => 'default'),
            'queue' => env('REDIS_QUEUE', fn (): string => 'default'),
        ],
    ],
];
