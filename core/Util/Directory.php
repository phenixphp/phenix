<?php

declare(strict_types=1);

namespace Core\Util;

class Directory
{
    /**
     * @return array<int, string>
     */
    public static function all(string $path): array
    {
        $paths = [];
        $files = \glob($path . '/*');

        foreach ($files as $file) {
            if (\is_dir($file)) {
                $paths = \array_merge($paths, self::all($file));

                continue;
            }

            if (\file_exists($file)) {
                $paths[] = $file;
            }
        }

        return $paths;
    }
}
