<?php

return [
    'default' => env('QUEUE_DRIVER', static fn (): string => 'database'),

    'drivers' => [
        'parallel' => [
            'timeout' => env('PARALLEL_QUEUE_TIMEOUT', static fn (): int => 2),
            'chunk_processing' => env('PARALLEL_QUEUE_CHUNK_PROCESSING', static fn (): bool => true),
            'chunk_size' => env('PARALLEL_QUEUE_CHUNK_SIZE', static fn (): int => 10),
            'max_retries' => env('PARALLEL_QUEUE_MAX_RETRIES', static fn (): int => 3),
            'retry_delay' => env('PARALLEL_QUEUE_RETRY_DELAY', static fn (): int => 2),
            'interval' => env('PARALLEL_QUEUE_INTERVAL', static fn (): float => 2.0),
        ],

        'database' => [
            'connection' => env('DB_QUEUE_CONNECTION', static fn (): string => 'mysql'),
            'table' => env('DB_QUEUE_TABLE', static fn (): string => 'tasks'),
            'queue' => env('DB_QUEUE', static fn (): string => 'default'),
        ],

        'redis' => [
            'connection' => env('REDIS_QUEUE_CONNECTION', static fn (): string => 'default'),
            'queue' => env('REDIS_QUEUE', static fn (): string => 'default'),
        ],
    ],
];
