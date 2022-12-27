<?php

declare(strict_types=1);

namespace Core\Runtime;

use BadMethodCallException as BadMethodCall;
use Core\App;

abstract class Facade
{
    /**
     * @param array<int, object|string|int|bool|null> $args
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $object = App::make(static::getKeyName());

        if (self::hasMethod($object, $method)) {
            return $object->{$method}(...$args);
        }

        $class = $object::class;

        throw new BadMethodCall("{$class} does not have method {$method}");
    }

    abstract protected static function getKeyName(): string;

    private static function hasMethod(object $object, string $method): bool
    {
        return method_exists($object, $method)
            && is_callable([$object, $method]);
    }
}
