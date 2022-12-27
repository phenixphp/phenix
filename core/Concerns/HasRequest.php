<?php

declare(strict_types=1);

namespace Core\Concerns;

use Amp\Http\Server\Request;
use Amp\Http\Server\Router;

trait HasRequest
{
    /**
     * @return array<string, array|string|int|bool|null>
     */
    protected function getAttrs(Request $request): array
    {
        return $request->getAttribute(Router::class);
    }

    protected function getAttr(Request $request, string $key): string
    {
        return $request->getAttribute(Router::class)[$key];
    }
}
