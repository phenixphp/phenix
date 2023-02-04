<?php

declare(strict_types=1);

namespace Core\Routing;

use Closure;

class RouteGroupBuilder extends RouteBuilder
{
    protected string $basePrefix;

    protected string $prefix = '';

    protected Closure $group;

    public function __construct(string $prefix = '', string $name = '', array $middleware = [])
    {
        $this->basePrefix = $prefix;
        $this->baseName = $name;
        $this->middlewares = $middleware;
    }

    public function name(string $name): self
    {
        $this->name = rtrim($this->baseName, '.') . '.' . trim($name, '.') . '.';

        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = rtrim($this->basePrefix, '/') . '/' . trim($prefix, '/') . '/';

        return $this;
    }

    public function middleware(array|string $middleware): self
    {
        $this->middlewares = array_merge($this->middlewares, (array) $middleware);

        return $this;
    }

    public function group(Closure $closure): void
    {
        $this->group = $closure;
    }

    public function toArray(): array
    {
        $route = new Route($this->name, $this->prefix, $this->middlewares);

        ($this->group)($route);

        return array_values($route->toArray());
    }
}
