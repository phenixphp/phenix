<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static string get(string $path, string $mode = 'r')
 */
class Storage extends Facade
{
    public static function getKeyName(): string
    {
        return 'storage';
    }
}
