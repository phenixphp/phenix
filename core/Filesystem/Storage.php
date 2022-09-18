<?php

declare(strict_types=1);

namespace Core\FileSystem;

use Amp\File\FileSystem;
use Throwable;

use function Amp\File\filesystem;

class Storage
{
    private FileSystem $file;

    public function __construct()
    {
        $this->file = filesystem();
    }

    public function get(string $path, string $mode = 'r'): string
    {
        $content = '';

        $this->file->openFile($path, $mode)
            ->onResolve(function (Throwable $error = null, $result = null) use (&$content) {
                if ($error) {
                    throw $error;
                }

                $content = $result ?? '';
            });

        return $content;
    }
}
