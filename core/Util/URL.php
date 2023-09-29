<?php

declare(strict_types=1);

namespace Core\Util;

use Core\Facades\Config;

class URL
{
    public static function build(string $path, array $parameters = []): string
    {
        $path = trim($path, '/');

        $port = Config::get('app.port');

        $url = Config::get('app.url');

        $uri = "{$url}:{$port}/{$path}";

        if (! empty($parameters)) {
            $uri .= '?' . http_build_query($parameters);
        }

        return $uri;
    }
}
