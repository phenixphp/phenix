<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static array|string|int|bool|null get(string $key)
 * @method static void set(string $key, array|string|int|bool|null $value)
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
