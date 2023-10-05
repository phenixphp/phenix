<?php

declare(strict_types=1);

namespace Tests\Util;

use Phenix\Constants\HttpMethods;
use Phenix\Routing\Route;

class AssertRoute
{
    public function __construct(private array $route)
    {
        // ..
    }

    public static function from(Route|array $route)
    {
        if ($route instanceof Route) {
            $route = $route->toArray()[0];
        }

        return new self($route);
    }

    public function methodIs(HttpMethods $method): self
    {
        expect($this->route[0])->toBe($method);

        return $this;
    }

    public function pathIs(string $path): self
    {
        expect($this->route[1])->toBe($path);

        return $this;
    }

    public function hasMiddlewares(array $middlewares): self
    {
        $expected = array_map(fn ($middleware) => get_class($middleware), $this->route[3]);

        expect($expected)->toMatchArray($middlewares);

        return $this;
    }

    public function nameIs(string $name): self
    {
        expect($this->route[4])->toBe($name);

        return $this;
    }

    public function containsParameters(array $parameters): self
    {
        expect($this->route[5])->toMatchArray($parameters);

        return $this;
    }
}
