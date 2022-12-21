<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static mixed get(string $key)
 * @method static void set(string $key, mixed $value)
 *
 * @see \Core\Runtime\Config
 */
class Config extends Facade
{
    public static function getKeyName(): string
    {
        return 'config';
    }
}
