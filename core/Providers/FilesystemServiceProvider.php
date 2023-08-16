<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Contracts\Filesystem\File as FileContract;
use Core\Filesystem\File;
use Core\Filesystem\Storage;

class FilesystemServiceProvider extends ServiceProvider
{
    public function provides(string $id): bool
    {
        return in_array($id, [Storage::class, FileContract::class], true);
    }

    public function boot(): void
    {
        $this->getContainer()->add(Storage::class);
        $this->getContainer()->add(FileContract::class, File::class);
    }
}
