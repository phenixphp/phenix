<?php

declare(strict_types=1);

namespace Core\Util;

class Namespacer
{
    public static function parse(string $path): string
    {
        $namespace = str_replace([APP_PATH . DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $path);

        return ucfirst($namespace);
    }
}
