<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendOtpController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\Guest;
use Phenix\Cache\RateLimit\Middlewares\RateLimiter;
use Phenix\Facades\Route;
use Phenix\Routing\Route as Router;

Route::get('/', [WelcomeController::class, 'index']);

Route::middleware(Guest::class)
    ->group(function (Router $router): void {
        $router->post('register', [RegisterController::class, 'store'])
            ->name('register');

        $router->post('verify-email', [VerifyEmailController::class, 'verify'])
            ->name('verification.verify')
            ->middleware(RateLimiter::perMinute(6));

        $router->post('resend-verification-otp', [ResendOtpController::class, 'resend'])
            ->name('verification.resend')
            ->middleware(RateLimiter::perMinute(2));
    });
