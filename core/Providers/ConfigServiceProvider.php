<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Runtime\Config;

class ConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bind(Config::class, Config::build(...))->setShared(true);
    }
}
