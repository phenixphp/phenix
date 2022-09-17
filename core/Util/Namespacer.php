<?php

namespace Core\Util;

class Namespacer
{
    public static function parse(string $namespace): string
    {
        $namespace = str_replace([APP_PATH . DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $namespace);

        return ucfirst($namespace);
    }
}
