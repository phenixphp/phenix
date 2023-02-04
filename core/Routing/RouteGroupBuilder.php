<?php

declare(strict_types=1);

namespace Core\Routing;

use Closure;

class RouteGroupBuilder extends RouteBuilder
{
    protected string|null $basePrefix = null;

    protected string|null $prefix = null;

    protected Route|null $group = null;

    public function __construct(string|null $prefix = null, string|null $name = null, array $middleware = [])
    {
        $this->basePrefix = $prefix;
        $this->baseName = $name;
        $this->middlewares = $middleware;
    }

    public function name(string $name): self
    {
        $this->name = rtrim($this->baseName ?: '', '.') . '.' . trim($name, '.') . '.';

        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = rtrim($this->basePrefix ?: '', '/') . '/' . trim($prefix, '/') . '/';

        return $this;
    }

    public function middleware(array|string $middleware): self
    {
        $this->middlewares = array_merge($this->middlewares, (array) $middleware);

        return $this;
    }

    public function group(Closure $closure): void
    {
        $group = new Route($this->name, $this->prefix, $this->middlewares);

        $closure($group);

        $this->group = $group;
    }

    public function toArray(): array
    {
        return array_values($this->group->toArray());
    }
}
