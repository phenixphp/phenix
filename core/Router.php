<?php

namespace Core;

use Core\Constants\Http;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\Router as ServerRouter;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;

class Router
{
    private ServerRouter $router;

    public function __construct()
    {
        $this->router = new ServerRouter();
    }

    public function getRouter(): ServerRouter
    {
        return $this->router;
    }

    public function get(string $uri, array $handler, Middleware ...$middlewares): void
    {
        $this->router->addRoute(Http::METHOD_GET, $uri, $this->callable($handler), ...$middlewares);
    }

    public function post(string $uri, array $handler, Middleware ...$middlewares): void
    {
        $this->router->addRoute(Http::METHOD_POST, $uri, $this->callable($handler), ...$middlewares);
    }

    public function put(string $uri, array $handler, Middleware ...$middlewares): void
    {
        $this->router->addRoute(Http::METHOD_PUT, $uri, $this->callable($handler), ...$middlewares);
    }

    public function patch(string $uri, array $handler, Middleware ...$middlewares): void
    {
        $this->router->addRoute(Http::METHOD_PATCH, $uri, $this->callable($handler), ...$middlewares);
    }

    public function delete(string $uri, array $handler, Middleware ...$middlewares): void
    {
        $this->router->addRoute(Http::METHOD_DELETE, $uri, $this->callable($handler), ...$middlewares);
    }

    private function callable(array $handler): CallableRequestHandler
    {
        [$controller, $method] = $handler;

        $controller = Container::get($controller);

        return new CallableRequestHandler($controller->{$method}(...));
    }
}
