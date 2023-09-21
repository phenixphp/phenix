<?php

declare(strict_types=1);

use App\Http\Controllers\WelcomeController;
use Core\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);
