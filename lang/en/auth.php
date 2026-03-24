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
        'reset_password' => [
            'subject' => 'Your password reset code',
            'title' => 'Password reset code',
            'message' => 'Use the following verification code to reset your password.',
        ],
        'email_verification' => [
            'subject' => 'Verify your email address',
            'title' => 'Email verification code',
            'message' => 'Use the following verification code to verify your email address.',
            'resent' => 'OTP has been resent successfully.',
        ],
    ],
    'password_reset' => [
        'sent' => 'If your email address exists in our records, a password reset code has been sent.',
        'reset' => 'Password has been reset successfully.',
    ],
    'security' => [
        'warning' => 'For your security:',
        'never_share' => 'Never share this code with anyone. Our team will never ask you for your verification code.',
        'ignore_if_not_requested' => 'If you didn\'t request this verification, please ignore this email.',
    ],
    'footer' => [
        'copyright' => ':year :appName. All rights reserved.',
    ],
    'rate_limit' => [
        'error' => 'Too Many Requests',
        'exceeded' => 'Rate limit exceeded. Please try again later.',
    ],
];
