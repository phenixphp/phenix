<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Console\Commands\Database\MakeMigration;
use Core\Console\Commands\Database\Migrate;
use Core\Console\Commands\Database\Rollback;
use Core\Database\Connections\ConnectionFactory;
use Core\Database\Constants\Connections;
use Core\Database\Constants\Drivers;
use Core\Facades\Config;
use League\Container\Argument\ResolvableArgument;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $connections = array_filter(array_keys(Config::get('database.connections')), function (string $connection) {
            return $connection !== Config::get('database.default');
        });

        foreach ($connections as $connection) {
            $settings = Config::get('database.connections.' . $connection);

            /** @var Drivers $driver */
            $driver = $settings['driver'];

            $callback = ConnectionFactory::make($driver, $settings);

            $this->bind(Connections::name($connection), $callback);
        }
    }

    public function boot(): void
    {
        $defaultConnection = Config::get('database.default');

        $settings = Config::get('database.connections.' . $defaultConnection);

        /** @var Drivers $driver */
        $driver = $settings['driver'];

        $callback = ConnectionFactory::make($driver, $settings);

        $this->bind(Connections::name('default'), new ResolvableArgument(Connections::name($defaultConnection)));

        $this->bind(Connections::name($defaultConnection), $callback);

        $this->commands([
            MakeMigration::class,
            Migrate::class,
            Rollback::class,
        ]);
    }
}
