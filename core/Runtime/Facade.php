<?php

namespace Core\Runtime;

use BadMethodCallException;
use Core\Container;

abstract class Facade
{
    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $object = Container::get(static::getKeyName());

        if (method_exists($object, $method) && is_callable([$object, $method])) {
            return $object->{$method}(...$arguments);
        }

        $class = get_class($object);

        throw new BadMethodCallException("{$class} does not have a named method {$method}");
    }

    abstract protected static function getKeyName(): string;
}
