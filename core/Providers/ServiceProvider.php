<?php

declare(strict_types=1);

namespace Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class ServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function register(): void
    {
        // ..
    }

    public function boot(): void
    {
        // ..
    }
}
