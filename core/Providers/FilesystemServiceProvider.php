<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Contracts\Filesystem\File as FileContract;
use Core\Filesystem\File;
use Core\Filesystem\Storage;

class FilesystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bind(Storage::class);
        $this->bind(FileContract::class, File::class);
    }
}
