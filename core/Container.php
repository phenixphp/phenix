<?php

namespace Core;

use BadMethodCallException;
use Core\Runtime\Config;
use Core\Util\Files;
use DI\ContainerBuilder;
use DI\Container as Storage;

use function DI\create;

class Container
{
    private static array $definitions;
    private static Storage $container;

    public static function build(): void
    {
        if (!isset(self::$container)) {
            self::loadControllers();

            $builder = new ContainerBuilder();
            $builder->addDefinitions(self::$definitions);

            self::$container = $builder->build();

            self::loadSettings();
        }
    }

    public static function __callStatic($method, $arguments)
    {
        if (self::hasMethod($method)) {
            return self::$container->{$method}(...$arguments);
        }

        throw new BadMethodCallException("Container does not have a named method {$method}");
    }

    public static function self(): Storage
    {
        return self::$container;
    }

    private static function hasMethod(string $method): bool
    {
        return method_exists(self::$container, $method) && is_callable([self::$container, $method]);
    }

    private static function loadControllers(): void
    {
        $controllers = Files::directory(self::getControllersPath());

        foreach ($controllers as $controller) {
            $controller = ucfirst(str_replace([APP_PATH . DIRECTORY_SEPARATOR, '.php'], '', $controller));

            self::$definitions[$controller] = create($controller);
        }
    }

    private static function getControllersPath(): string
    {
        return base_path('app'. DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers');
    }

    private static function loadSettings(): void
    {
        $config = Config::build();

        self::$container->set('config', $config);
    }
}
