<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Console\Phenix;
use League\Container\Definition\DefinitionInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class ServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(protected array $provided = [])
    {
        // ..
    }

    public function provides(string $id): bool
    {
        return in_array($id, $this->provided, true);
    }

    public function register(): void
    {
        // ..
    }

    public function boot(): void
    {
        // ..
    }

    public function bind(string $key, mixed $concrete = null): DefinitionInterface
    {
        $this->provided[] = $key;

        return $this->getContainer()->add($key, $concrete);
    }

    protected function commands(array|string $commands): void
    {
        Phenix::pushCommands((array) $commands);
    }
}
