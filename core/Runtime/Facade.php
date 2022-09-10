<?php

namespace Core\Runtime;

use Core\Container;

abstract class Facade
{
    abstract protected static function getKeyName(): string;

    /**
     * @param string $method
     * @param array $arguments
     * @return object
     */
    public static function __callStatic($method, $arguments)
    {
        $object = Container::get(static::getKeyName());

        return $object->{$method}(...$arguments);
    }
}
