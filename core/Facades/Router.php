<?php

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static mixed get(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static mixed post(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static mixed put(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static mixed patch(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static mixed delete(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 */
class Router extends Facade
{
    public static function getKeyName(): string
    {
        return 'router';
    }
}
