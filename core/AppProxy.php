<?php

declare(strict_types=1);

namespace Core;

use Core\Contracts\App as AppContract;

class AppProxy implements AppContract
{
    public function __construct(
        private App $app
    ) {
        // ..
    }

    public function run(): void
    {
        $this->app->run();
    }

    public function stop(): void
    {
        $this->app->stop();
    }

    public function swap(string $key, object $concrete): void
    {
        $this->app->swap($key, $concrete);
    }

    public function enableTestingMode(): void
    {
        $this->app->disableSignalTrapping();
    }
}
