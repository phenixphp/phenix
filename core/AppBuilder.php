<?php

declare(strict_types=1);

namespace Core;

use Core\Contracts\App as AppContract;
use Core\Contracts\Buildable;
use Core\Util\Directory;

class AppBuilder implements Buildable
{
    public static function build(): AppContract
    {
        $app = new App(dirname(__DIR__));
        $app->setup();

        self::loadRoutes();

        $app->setRouter();

        return new AppProxy($app);
    }
}
