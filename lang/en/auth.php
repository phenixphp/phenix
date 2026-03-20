<?php

declare(strict_types=1);

return [
    'unauthorized' => 'Unauthorized',
    'login' => [
        'invalid_credentials' => 'Invalid credentials.',
    ],
    'logout' => [
        'success' => 'Logged out successfully.',
    ],
    'email_verification' => [
        'verified' => 'Email verified successfully.',
    ],
    'otp' => [
        'invalid' => 'The provided OTP is invalid.',
        'limit_exceeded' => 'You have exceeded the maximum number of OTP requests. Please try again later.',
        'label' => 'Verification code',
        'expiry' => 'This code expires in :minutes minutes.',
        'login' => [
            'subject' => 'Your login verification code',
            'title' => 'Login verification code',
            'message' => 'Use the following verification code to complete your sign in.',
            'sent' => 'A verification code has been sent to your email address.',
        ],
        'email_verification' => [
            'subject' => 'Verify your email address',
            'title' => 'Email verification code',
            'message' => 'Use the following verification code to verify your email address.',
            'resent' => 'OTP has been resent successfully.',
        ],
    ],
    'rate_limit' => [
        'error' => 'Too Many Requests',
        'exceeded' => 'Rate limit exceeded. Please try again later.',
    ],
];
