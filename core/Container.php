<?php

namespace Core;

use Core\Concerns\Singleton;
use Core\Runtime\Config;
use Core\Util\Files;
use League\Container\Container as Storage;

class Container
{
    use Singleton;

    private static Storage $container;

    public static function build(): void
    {
        if (!isset(self::$container)) {
            self::$container = new Storage();

            self::addConfig();
            self::addRouter();
            self::addControllers();
        }
    }

    public static function get(string $id): mixed
    {
        return self::$container->get($id);
    }

    public static function getContainer(): Storage
    {
        return self::$container;
    }

    private static function addConfig(): void
    {
        self::$container->add('config', Config::build(...))->setShared(true);
    }

    private static function addRouter(): void
    {
        self::$container->add('router', Router::class)->setShared(true);
    }

    private static function addControllers(): void
    {
        $controllers = Files::directory(self::getControllersPath());

        foreach ($controllers as $controller) {
            $controller = self::parseNamespace($controller);

            self::$container->add($controller);
        }
    }

    private static function getControllersPath(): string
    {
        return base_path('app'. DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers');
    }

    private static function parseNamespace(string $namespace): string
    {
        $namespace = str_replace([APP_PATH . DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $namespace);

        return ucfirst($namespace);
    }
}
