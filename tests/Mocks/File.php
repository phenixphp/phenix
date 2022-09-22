<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Core\Filesystem\File as FileBase;

class File extends FileBase
{
    public function put(string $path, string $content): bool
    {
        $path = $this->getTempPath($path);

        return file_put_contents($path, $content) !== false;
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getTempPath($path));
    }

    private function getTempPath(string $path): string
    {
        $path = explode('/', $path);

        return sys_get_temp_dir() . '/' . array_pop($path);
    }
}
