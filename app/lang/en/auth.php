<?php

declare(strict_types=1);

return [
    'otp' => [
        'email_verification' => [
            'title' => 'Verify Your Email Address',
            'subject' => 'Verify Your Email Address',
            'message' => 'Please use the following One-Time Password (OTP) to verify your email address:',
        ],
        'login' => [
            'title' => 'Login Verification Code',
            'subject' => 'Login Verification Code',
            'message' => 'Please use the following One-Time Password (OTP) to log in to your account:',
        ],
        'label' => 'Your one-time password code',
        'expiry' => 'Valid for :minutes minutes',
        'sent' => 'A verification code has been sent to your email address.',
        'verified' => 'Your verification code has been confirmed successfully.',
        'expired' => 'The verification code has expired. Please request a new one.',
        'invalid' => 'The verification code is invalid.',
        'already_used' => 'This verification code has already been used.',
    ],

    'security' => [
        'warning' => '⚠️ For your security:',
        'never_share' => 'Never share this code with anyone. Our team will never ask you for your verification code.',
        'ignore_if_not_requested' => 'If you didn\'t request this verification, please ignore this email.',
    ],

    'footer' => [
        'copyright' => ':year :appName. All rights reserved.',
    ],
];
