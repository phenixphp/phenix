<?php

declare(strict_types=1);

use App\Http\Controllers\WelcomeController;
use Core\Facades\Router;

Router::get('/', [WelcomeController::class, 'index']);
