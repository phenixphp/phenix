<?php

declare(strict_types=1);

namespace Core\Routing;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;
use Amp\Http\Server\Router as ServerRouter;
use Core\App;
use Core\Constants\Http;

class Router
{
    private ServerRouter $router;

    public function __construct(HttpServer $httpServer, ErrorHandler $errorHandler)
    {
        $this->router = new ServerRouter($httpServer, $errorHandler);
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

    private function callable(array $handler): ClosureRequestHandler
    {
        [$controller, $method] = $handler;

        $controller = App::make($controller);

        return new ClosureRequestHandler($controller->{$method}(...));
    }
}
