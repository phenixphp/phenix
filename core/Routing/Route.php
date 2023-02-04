<?php

declare(strict_types=1);

namespace Core\Routing;

use Amp\Http\Server\RequestHandler\ClosureRequestHandler;
use Closure;
use Core\App;
use Core\Constants\Http\Methods;
use Core\Contracts\Arrayable;

class Route implements Arrayable
{
    private array $collection;

    public function __construct(
        private string $baseName = '',
        private string $prefix = '',
        private array $middleware = [],
    ) {
        $this->collection = [];
    }

    /**
     * @param Closure|array<int, string> $handler
     */
    public function get(string $uri, Closure|array $handler): RouteBuilder
    {
        return $this->addRoute(Methods::GET, $uri, $handler);
    }

    public function post(string $uri, Closure|array $handler): RouteBuilder
    {
        return $this->addRoute(Methods::POST, $uri, $handler);
    }

    public function put(string $uri, Closure|array $handler): RouteBuilder
    {
        return $this->addRoute(Methods::PUT, $uri, $handler);
    }

    public function patch(string $uri, Closure|array $handler): RouteBuilder
    {
        return $this->addRoute(Methods::PATCH, $uri, $handler);
    }

    public function delete(string $uri, Closure|array $handler): RouteBuilder
    {
        return $this->addRoute(Methods::DELETE, $uri, $handler);
    }

    public function name(string $name): RouteGroupBuilder
    {
        $group = new RouteGroupBuilder($this->prefix, $this->baseName, $this->middleware);
        $group->name($name);

        $this->collection[] = $group;

        return $group;
    }

    public function prefix(string $prefix): RouteGroupBuilder
    {
        $group = new RouteGroupBuilder($this->prefix, $this->baseName, $this->middleware);
        $group->prefix($prefix);

        $this->collection[] = $group;

        return $group;
    }

    public function middleware(array|string $middleware): RouteGroupBuilder
    {
        $group = new RouteGroupBuilder($this->prefix, $this->baseName, $this->middleware);
        $group->middleware($middleware);

        $this->collection[] = $group;

        return $group;
    }

    public function toArray(): array
    {
        return array_reduce($this->collection, function (array $routes, Arrayable $item) {
            if ($item instanceof RouteGroupBuilder) {
                return array_merge($routes, $item->toArray());
            }

            array_push($routes, $item->toArray());

            return $routes;
        }, []);
    }

    private function addRoute(Methods $method, string $uri, Closure|array $handler): RouteBuilder
    {
        $route = new RouteBuilder(
            $method,
            $this->prefix . $uri,
            $this->callable($handler),
            $this->baseName,
            $this->middleware
        );

        $this->collection[] = $route;

        return $route;
    }

    /**
     * @param array<int, string> $handler
     */
    private function callable(Closure|array $handler): ClosureRequestHandler
    {
        if (\is_array($handler)) {
            [$controller, $method] = $handler;

            $controller = App::make($controller);

            $handler = $controller->{$method}(...);
        }

        return new ClosureRequestHandler($handler);
    }
}
