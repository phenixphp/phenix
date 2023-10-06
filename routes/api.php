<?php

declare(strict_types=1);

use App\Http\Controllers\WelcomeController;
use Phenix\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);
