<?php

declare(strict_types=1);

use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\AcceptJsonResponses;
use Core\Facades\Router;

Router::get('/', [WelcomeController::class, 'index'], new AcceptJsonResponses());
