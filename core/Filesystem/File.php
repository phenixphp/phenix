<?php

declare(strict_types=1);

namespace Core\Filesystem;

use Amp\File\FileSystem;
use Core\Contracts\Filesystem\File as FileContract;
use Throwable;

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
        $content = '';

        $this->driver->openFile($path, $mode)
            ->onResolve(function (Throwable $error = null, $result = null) use (&$content) {
                if ($error) {
                    throw $error;
                }

                $content = $result ?? '';
            });

        return $content;
    }

    public function put(string $path, string $content): bool
    {
        return file_put_contents($path, $content) !== false;
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }
}
