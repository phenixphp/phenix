<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static void get(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static void post(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static void put(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static void patch(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 * @method static void delete(string $uri, array $handler, \Amp\Http\Server\Middleware ...$middlewares)
 */
class Router extends Facade
{
    public static function getKeyName(): string
    {
        return 'router';
    }
}
