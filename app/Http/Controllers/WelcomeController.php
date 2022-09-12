<?php

namespace App\Http\Controllers;

use Amp\Http\Status;
use Amp\Http\Server\Response;
use Core\Http\Controller;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        return new Response(Status::OK, ['content-type' => 'text/plain'], 'Hello, world!');
    }
}
