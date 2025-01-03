<?php

return [
    'origins' => env('CORS_ORIGIN', fn () => ['http://localhost', 'http://127.0.0.1']),
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'],
    'max_age' => 8600,
    'allowed_headers' => ['X-Request-Headers', 'Content-Type', 'Authorization', 'X-Requested-With'],
    'exposable_headers' => [],
    'allow_credentials' => false,
];
