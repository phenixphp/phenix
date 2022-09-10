<?php

use App\Http\Controllers\WelcomeController;
use Core\Router;

Router::get('/', WelcomeController::index(...));