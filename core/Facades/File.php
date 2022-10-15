<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Contracts\Filesystem\File as FileContract;
use Core\Runtime\Facade;

/**
 * @method static string get(string $path, string $mode = 'r')
 * @method static bool put(string $path, string $content)
 * @method static bool exists(string $path)
 * @method static bool isDirectory(string $path)
 * @method static bool isFile(string $path)
 * @method static void createDirectory(string $path, int $mode = 0755)
 *
 * @see \Core\Filesystem\File
 */
class File extends Facade
{
    public static function getKeyName(): string
    {
        return FileContract::class;
    }
}
