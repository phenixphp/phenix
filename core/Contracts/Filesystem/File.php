<?php

declare(strict_types=1);

namespace Core\Contracts\Filesystem;

interface File
{
    public function get(string $path, string $mode = 'r'): string;
    public function put(string $path, string $content): bool;
    public function exists(string $path): bool;
}