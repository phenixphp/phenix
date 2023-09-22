<?php

declare(strict_types=1);

namespace Core;

use Core\Contracts\App as AppContract;
use Core\Contracts\Buildable;
use Core\Runtime\Environment;

class AppBuilder implements Buildable
{
    public static function build(): AppContract
    {
        $app = new App(dirname(__DIR__));

        Environment::load();

        $app->setup();

        return new AppProxy($app);
    }
}
