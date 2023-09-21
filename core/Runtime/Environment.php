<?php

declare(strict_types=1);

namespace Core\Runtime;

use Core\AppProxy;
use Dotenv\Dotenv;

class Environment
{
    public static function load(): void
    {
        $name = null;

        if (AppProxy::testingModeEnabled() && file_exists(base_path('.env.testing'))) {
            $name = '.env.testing';
        }

        Dotenv::createImmutable(base_path(), $name)->load();
    }
}
