<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendVerificationOtpController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\Guest;
use Phenix\Auth\Middlewares\Authenticated;
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
            ->middleware(RateLimiter::perMinute(6, 'auth:verify-email'));

        $router->post('resend-verification-otp', [ResendVerificationOtpController::class, 'resend'])
            ->name('verification.resend')
            ->middleware(RateLimiter::perMinute(2, 'auth:resend-verification-otp'));

        $router->post('login', [LoginController::class, 'login'])
            ->name('login')
            ->middleware(RateLimiter::perMinute(5, 'auth:login'));

        $router->post('login/authorize', [LoginController::class, 'authorize'])
            ->name('login.authorize')
            ->middleware(RateLimiter::perMinute(5, 'auth:login-authorize'));
    });

Route::middleware(Authenticated::class)
    ->group(function (Router $router): void {
        $router->post('logout', [LoginController::class, 'logout'])
            ->name('logout');
    });
