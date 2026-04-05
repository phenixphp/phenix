<?php

declare(strict_types=1);

return [
    'origins' => env('CORS_ORIGIN', static fn (): array => ['*']),
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'],
    'max_age' => 8600,
    'allowed_headers' => ['X-Request-Headers', 'Content-Type', 'Authorization', 'X-Requested-With'],
    'exposable_headers' => [],
    'allow_credentials' => false,
];
