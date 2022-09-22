<?php

declare(strict_types=1);

namespace Core\Filesystem;

use Amp\File\FileSystem;
use Throwable;

use function Amp\File\filesystem;

class Storage
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
}
