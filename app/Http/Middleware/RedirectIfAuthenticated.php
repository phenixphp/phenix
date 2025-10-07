<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Amp\Http\Server\Middleware;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;

class RedirectIfAuthenticated implements Middleware
{
    public function handleRequest(Request $request, RequestHandler $next): Response
    {
        return $next->handleRequest($request);
    }
}
