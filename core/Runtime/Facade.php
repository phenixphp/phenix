<?php

declare(strict_types=1);

namespace Core\Runtime;

use BadMethodCallException;
use Core\App;

abstract class Facade
{
    /**
     * @param array<int, mixed> $arguments
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $object = App::make(static::getKeyName());

        if (method_exists($object, $method) && is_callable([$object, $method])) {
            return $object->{$method}(...$arguments);
        }

        $class = $object::class;

        throw new BadMethodCallException("{$class} does not have a named method {$method}");
    }

    abstract protected static function getKeyName(): string;
}
