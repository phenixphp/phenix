<?php

declare(strict_types=1);

use Core\App;
use Core\Http\Response;

if (! function_exists('base_path()')) {
    function base_path(string $path = ''): string
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);

        return App::path() . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
    }
}

if (! function_exists('response')) {
    function response(): Response
    {
        return new Response();
    }
}

if (! function_exists('env')) {
    function env(string $key, Closure|null $default = null): string|int|bool|null
    {
        $value = $_ENV[$key] ?? null;

        if ($value) {
            return match ($value) {
                'true' => true,
                'false' => false,
                default => $value,
            };
        }

        return $default instanceof Closure ? $default() : $default;
    }
}
