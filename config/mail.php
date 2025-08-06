<?php

return [
    'default' => env('MAIL_MAILER', static fn (): string => 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', static fn (): string => 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', static fn (): int => 587),
            'encryption' => env('MAIL_ENCRYPTION', static fn (): string => 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'resend' => [
            'transport' => 'resend',
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', static fn (): string => 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', static fn (): string => 'Example'),
    ],
];
