<?php

use App\Http\Controllers\WelcomeController;
use Core\Facades\Router;

Router::get('/', [WelcomeController::class, 'index']);
