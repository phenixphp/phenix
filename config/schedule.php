<?php

declare(strict_types=1);

return [
    'connection' => env('SCHEDULE_REDIS_CONNECTION', static fn (): string => 'default'),
    'lock_prefix' => env('SCHEDULE_LOCK_PREFIX', static fn (): string => 'phenix:schedule:'),
    'occurrence_ttl' => env('SCHEDULE_OCCURRENCE_TTL', static fn (): int => 86400),
];
