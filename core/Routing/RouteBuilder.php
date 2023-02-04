<?php

declare(strict_types=1);

namespace Core\Routing;

use Amp\Http\Server\Middleware;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;
use Core\Constants\Http\Methods;
use Core\Contracts\Arrayable;

class RouteBuilder implements Arrayable
{
    protected string|null $baseName = null;

    protected string|null $name = null;

    /**
     * @var array<int, string>
     */
    protected array $parameters = [];

    /**
     * @var array<int, \Amp\Http\Server\Middleware|string|null>
     */
    protected array $middlewares = [];

    public function __construct(
        protected Methods $method,
        protected string $path,
        protected ClosureRequestHandler $closure,
        string|null $name = null,
        array $middleware = [],
    ) {
        $this->parameters = $this->extractParams($path);
        $this->baseName = $name;
        $this->middleware($middleware);
    }

    public function name(string $name): self
    {
        $this->name = $this->baseName . trim($name, '.');

        return $this;
    }

    public function middleware(array|string $middleware): self
    {
        foreach ((array) $middleware as $item) {
            $this->pushMiddleware(new $item());
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            $this->method,
            '/' . trim($this->path, '/'),
            $this->closure,
            $this->middlewares,
            $this->name ? trim($this->name, '.') : null,
            $this->parameters,
        ];
    }

    protected function extractParams(string $path): array
    {
        preg_match_all('/\{(\w+)\}/', $path, $params);

        return array_unique($params[1]);
    }

    protected function pushMiddleware(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}
