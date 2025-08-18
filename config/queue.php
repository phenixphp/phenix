<?php

return [
    'default' => env('QUEUE_DRIVER', fn (): string => 'database'),

    'drivers' => [
        'parallel' => [
            'timeout' => env('PARALLEL_QUEUE_TIMEOUT', fn (): int => 2),
            'chunk_processing' => env('PARALLEL_QUEUE_CHUNK_PROCESSING', fn (): bool => true),
            'chunk_size' => env('PARALLEL_QUEUE_CHUNK_SIZE', fn (): int => 10),
            'max_retries' => env('PARALLEL_QUEUE_MAX_RETRIES', fn (): int => 3),
            'retry_delay' => env('PARALLEL_QUEUE_RETRY_DELAY', fn (): int => 2),
            'interval' => env('PARALLEL_QUEUE_INTERVAL', fn (): float => 2.0),
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
