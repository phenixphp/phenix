<?php

declare(strict_types=1);

return [
    'users' => [
        'model' => Phenix\Auth\User::class,
    ],
    'tokens' => [
        'model' => Phenix\Auth\PersonalAccessToken::class,
        'prefix' => '',
        'expiration' => 60 * 12, // in minutes
        'rate_limit' => [
            'attempts' => 5,
            'window' => 300, // window in seconds
        ],
    ],
    'otp' => [
        'expiration' => 10, // in minutes
    ],
];
