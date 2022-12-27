<?php

declare(strict_types=1);

namespace Core\Util;

use Core\App;

class NamespaceResolver
{
    public static function parse(string $path): string
    {
        $base = App::path() . DIRECTORY_SEPARATOR;

        $namespace = str_replace([$base, '.php', '/'], ['', '', '\\'], $path);

        return ucfirst($namespace);
    }
}
