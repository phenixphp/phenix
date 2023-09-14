<?php

declare(strict_types=1);

namespace Core\Console\Commands\Database;

use Core\Database\Constants\Drivers;
use Core\Facades\Config;
use Phinx\Config\Config as MigrationConfig;
use Phinx\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputOption;

abstract class DatabaseCommand extends AbstractCommand
{
    public function __construct()
    {
        $defaultConnection = Config::get('database.default');

        $settings = Config::get('database.connections.' . $defaultConnection);

        /** @var Drivers $driver */
        $driver = $settings['driver'];

        $this->config = new MigrationConfig([
            'paths' => [
                'migrations' => Config::get('database.paths.migrations'),
                'seeds' => Config::get('database.paths.seeds'),
            ],
            'environments' => [
                'default_migration_table' => 'migrations',
                'default_environment' => 'default',
                'default' => [
                    'adapter' => $driver->value,
                    'host' => $settings['host'],
                    'name' => $settings['database'],
                    'user' => $settings['username'],
                    'pass' => $settings['password'],
                    'port' => $settings['port'],
                ],
            ],
        ]);

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption('--no-info', null, InputOption::VALUE_NONE, 'Hides all debug information');
    }
}
