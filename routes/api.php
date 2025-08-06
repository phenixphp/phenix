<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Phenix\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);

Route::get('/users', [UserController::class, 'index']);

Route::get('/users/{user}', [UserController::class, 'show']);

Route::post('/users', [UserController::class, 'store']);
