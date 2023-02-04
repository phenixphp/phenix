<?php

declare(strict_types=1);

namespace Tests\Util;

use Core\Constants\Http\Methods;
use Core\Routing\Route;
use Core\Routing\RouteBuilder;
use ReflectionObject;

class RouteParser
{
    private RouteBuilder $route;

    public function __construct(Route $router)
    {
        $this->route = $router->toArray()[0];
    }

    public function assertMethodIs(Methods $method): self
    {
        $routeReflection = new ReflectionObject($this->route);

        $property = $routeReflection->getProperty('method');
        $property->setAccessible(true);

        expect($property->getValue($this->route))->toBe($method);

        return $this;
    }

    public function assertNameIs(string $name): self
    {
        $routeReflection = new ReflectionObject($this->route);

        $property = $routeReflection->getProperty('name');
        $property->setAccessible(true);

        expect($property->getValue($this->route))->toBe($name);

        return $this;
    }

    public function assertContainsParameters(array $parameters): self
    {
        $routeReflection = new ReflectionObject($this->route);

        $property = $routeReflection->getProperty('parameters');
        $property->setAccessible(true);

        expect($property->getValue($this->route))->toBeArray($parameters);

        return $this;
    }

    public function assertHasMiddlewares(array $middlewares): self
    {
        $routeReflection = new ReflectionObject($this->route);

        $property = $routeReflection->getProperty('middlewares');
        $property->setAccessible(true);

        $routeMiddlewares = $property->getValue($this->route);
        $currentMiddlewares = [];

        foreach ($routeMiddlewares as $middleware) {
            $currentMiddlewares[] = get_class($middleware);
        }

        expect($currentMiddlewares)->toBeArray($middlewares);

        return $this;
    }
}
