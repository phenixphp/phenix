<?php

declare(strict_types=1);

namespace Core\Filesystem;

use Amp\File\Filesystem;

use function Amp\File\filesystem;

use Core\Contracts\Filesystem\File as FileContract;

class File implements FileContract
{
    private Filesystem $driver;

    public function __construct()
    {
        $this->driver = filesystem();
    }

    public function get(string $path): string
    {
        return $this->driver->read($path);
    }

    public function put(string $path, string $content): void
    {
        $this->driver->write($path, $content);
    }

    public function exists(string $path): bool
    {
        return $this->driver->exists($path);
    }

    public function isDirectory(string $path): bool
    {
        return $this->driver->isDirectory($path);
    }

    public function isFile(string $path): bool
    {
        return $this->driver->isFile($path);
    }

    public function createDirectory(string $path, int $mode = 0755): void
    {
        $this->driver->createDirectory($path, $mode);
    }
}
