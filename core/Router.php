<?php

namespace Core;

use Core\Constants\Http;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Router as ServerRouter;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;

class Router
{
    private static ServerRouter $router;

    public static function init(): void
    {
        if (!isset(self::$router)) {
            self::$router = new ServerRouter();
        }
    }

    public static function getRouter(): ServerRouter
    {
        return self::$router;
    }

    public static function get(string $uri, callable $requestHandler, Middleware ...$middlewares): void
    {
        self::$router->addRoute(Http::METHOD_GET, $uri, new CallableRequestHandler($requestHandler), ...$middlewares);
    }

    public static function post(string $uri, RequestHandler $requestHandler, Middleware ...$middlewares): void
    {
        self::$router->addRoute(Http::METHOD_POST, $uri, $requestHandler, ...$middlewares);
    }

    public static function put(string $uri, RequestHandler $requestHandler, Middleware ...$middlewares): void
    {
        self::$router->addRoute(Http::METHOD_PUT, $uri, $requestHandler, ...$middlewares);
    }

    public static function patch(string $uri, RequestHandler $requestHandler, Middleware ...$middlewares): void
    {
        self::$router->addRoute(Http::METHOD_PATCH, $uri, $requestHandler, ...$middlewares);
    }

    public function delete(string $uri, RequestHandler $requestHandler, Middleware ...$middlewares): void
    {
        self::$router->addRoute(Http::METHOD_DELETE, $uri, $requestHandler, ...$middlewares);
    }
}
