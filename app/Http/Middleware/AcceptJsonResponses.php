<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Amp\Http\Server\Middleware;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Status;

class AcceptJsonResponses implements Middleware
{
    public function handleRequest(Request $request, RequestHandler $next): Response
    {
        if ($this->acceptHtml($request)) {
            return response()->json([], Status::NOT_ACCEPTABLE);
        }

        return $next->handleRequest($request);
    }

    private function acceptHtml(Request $request): bool
    {
        return $request->hasHeader('Accept')
            && str_contains($request->getHeader('Accept'), 'html');
    }
}
