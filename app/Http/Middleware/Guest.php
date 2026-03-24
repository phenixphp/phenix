<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Amp\Http\Server\Middleware;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Phenix\Http\Constants\HttpStatus;

class Guest implements Middleware
{
    public function handleRequest(Request $request, RequestHandler $next): Response
    {
        $token = $this->extractToken($request->getHeader('Authorization'));

        if ($token === null) {
            return $next->handleRequest($request);
        }

        return $this->unauthorized();
    }

    protected function hasBearerToken(string|null $authorizationHeader): bool
    {
        return $authorizationHeader !== null
            && trim($authorizationHeader) !== ''
            && str_starts_with($authorizationHeader, 'Bearer ');
    }

    protected function extractToken(string|null $authorizationHeader): string|null
    {
        if (! $this->hasBearerToken($authorizationHeader)) {
            return null;
        }

        $parts = explode(' ', $authorizationHeader, 2);

        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    protected function unauthorized(): Response
    {
        return response()->json([
            'message' => trans('auth.unauthorized'),
        ], HttpStatus::UNAUTHORIZED)->send();
    }
}
