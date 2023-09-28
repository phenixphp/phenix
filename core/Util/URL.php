<?php

declare(strict_types=1);

namespace Core\Util;

use Core\Facades\Config;
use League\Uri\Components\Query;
use League\Uri\Uri;

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

    public static function fromUri(Uri $uri): string
    {
        return self::build($uri->getPath(), Query::fromUri($uri)->parameters());
    }
}