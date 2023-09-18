<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Routing\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bind(Route::class)->setShared(true);
    }
}
