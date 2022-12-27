<?php

declare(strict_types=1);

namespace Core\Filesystem;

use Amp\File\Filesystem;

use function Amp\File\filesystem;

class Storage
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
}
