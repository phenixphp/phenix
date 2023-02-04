<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static \Core\Routing\RouteBuilder get(string $uri, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder post(string $uri, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder put(string $uri, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder patch(string $uri, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder delete(string $uri, \Closure|array $handler)
 */
class Route extends Facade
{
    public static function getKeyName(): string
    {
        return 'route';
    }

}
