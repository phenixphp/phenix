<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Runtime\Config;

class ConfigServiceProvider extends ServiceProvider
{
    public function provides(string $id): bool
    {
        return in_array($id, [Config::class], true);
    }

    public function boot(): void
    {
        $this->getContainer()->add(
            Config::class,
            Config::build(...)
        )->setShared(true);
    }
}
