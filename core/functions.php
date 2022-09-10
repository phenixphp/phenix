<?php

if (!function_exists('base_path()')) {
    function base_path(string $path = ''): string {
        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            $path = ltrim($path, DIRECTORY_SEPARATOR);
        }

        if (str_ends_with($path, DIRECTORY_SEPARATOR)) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);
        }

        return APP_PATH . DIRECTORY_SEPARATOR . $path;
    }
}

// if (!function_exists('config')) {
//     function config(string $path): array|string
//     {
//         $path = explode('.', $path);
//         $file = array_shift($path);

//         if (!base_path('config' . DIRECTORY_SEPARATOR . $file)) {
//             throw new RuntimeException("Error Processing Request");
//         }
//     }
// }
