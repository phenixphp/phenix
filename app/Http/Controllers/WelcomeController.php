<?php

namespace App\Http\Controllers;

use Amp\Http\Status;
use Amp\Http\Server\Response;

class WelcomeController
{
    public static function index(): Response
    {
        return new Response(Status::OK, ['content-type' => 'text/plain'], 'Hello, world!');

    }
}
