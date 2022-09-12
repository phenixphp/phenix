<?php

namespace Core\Concerns;

use Core\Exceptions\RuntimeException;

trait Singleton
{
    private function __construct()
    {
        // Disabled instantiation.
    }

    final public function __clone(): void
    {
        throw new RuntimeException('Cloning was disabled.');
    }

    final public function __wakeup(): void
    {
        throw new RuntimeException('WakeUp was disabled.');
    }
}
