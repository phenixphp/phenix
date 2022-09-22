<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Contracts\Filesystem\File as FileContract;
use Core\Runtime\Facade;

/**
 * @method static string get(string $path, string $mode = 'r')
 * @method static bool put(string $path, string $content)
 * @method static bool exists(string $path)
 */
class File extends Facade
{
    public static function getKeyName(): string
    {
        return FileContract::class;
    }
}
