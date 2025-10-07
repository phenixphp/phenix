<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\RedirectIfAuthenticated;
use Phenix\Facades\Route;
use Phenix\Routing\Route as Router;

Route::get('/', [WelcomeController::class, 'index']);

Route::middleware(RedirectIfAuthenticated::class)->group(function (Router $router): void {
    $router->post('register', [RegisterController::class, 'store']);
});
