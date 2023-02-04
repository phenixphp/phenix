<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static \Core\Routing\RouteBuilder get(string $, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder post(string $path, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder put(string $path, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder patch(string $path, \Closure|array $handler)
 * @method static \Core\Routing\RouteBuilder delete(string $path, \Closure|array $handler)
 * @method static \Core\Routing\RouteGroupBuilder name(string $name)
 * @method static \Core\Routing\RouteGroupBuilder prefix(string $prefix)
 * @method static \Core\Routing\RouteGroupBuilder middleware(array|string $middleware)
 */
class Route extends Facade
{
    public static function getKeyName(): string
    {
        return 'route';
    }

}
