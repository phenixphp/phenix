<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Amp\Http\Server\Response;
use Core\Http\Controller;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        return response()->plain('Hello, world!' . PHP_EOL);
    }
}
