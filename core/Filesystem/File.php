<?php

declare(strict_types=1);

namespace Core\Filesystem;

use Amp\File\FileSystem;
use Core\Contracts\Filesystem\File as FileContract;

use function Amp\File\filesystem;

class File implements FileContract
{
    private FileSystem $driver;

    public function __construct()
    {
        $this->driver = filesystem();
    }

    public function get(string $path, string $mode = 'r'): string
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
}
