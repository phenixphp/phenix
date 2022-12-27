<?php

declare(strict_types=1);

use Core\App;
use Core\Http\Response;

if (! function_exists('base_path()')) {
    function base_path(string $path = ''): string
    {
        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            $path = ltrim($path, DIRECTORY_SEPARATOR);
        }

        if (str_ends_with($path, DIRECTORY_SEPARATOR)) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);
        }

        return App::path() . DIRECTORY_SEPARATOR . $path;
    }
}

if (! function_exists('response')) {
    function response(): Response
    {
        return new Response();
    }
}

if (! function_exists('env')) {
    function env(string $key, callable $default): string|int|bool
    {
        $value = getenv($key);

        return $value ? $value : $default();
    }
}
