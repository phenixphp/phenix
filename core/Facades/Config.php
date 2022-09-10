<?php

namespace Core\Facades;
use Core\Runtime\Facade;

/**
 * @method static mixed get(string $key)
 * @method static mixed set(string $key, mixed $value)
 */
class Config extends Facade
{
    public static function getKeyName(): string
    {
        return 'config';
    }
}
