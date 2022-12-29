<?php

declare(strict_types=1);

namespace Core\Contracts\Filesystem;

interface File
{
    public function get(string $path): string;

    public function put(string $path, string $content): void;

    public function exists(string $path): bool;

    public function isDirectory(string $path): bool;

    public function isFile(string $path): bool;

    public function createDirectory(string $path, int $mode = 0777): void;
}
