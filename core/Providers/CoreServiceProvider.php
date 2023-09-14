<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Console\Commands\MakeController;
use Core\Console\Commands\MakeMiddleware;
use Core\Console\Commands\MakeTest;

class CoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            MakeTest::class,
            MakeController::class,
            MakeMiddleware::class,
        ]);
    }
}
