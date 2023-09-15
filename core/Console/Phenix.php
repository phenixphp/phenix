<?php

declare(strict_types=1);

namespace Core\Console;

use Symfony\Component\Console\Application;

class Phenix extends Application
{
    private static array $commands;

    public function __construct()
    {
        parent::__construct('Phenix', '0.0.1');
    }

    public static function pushCommand(string $command): void
    {
        self::$commands[] = $command;
    }

    public static function pushCommands(array $commands): void
    {
        foreach ($commands as $command) {
            self::pushCommand($command);
        }
    }

    public function registerCommands(): void
    {
        foreach (self::$commands as $command) {
            $this->add(new $command());
        }
    }
}
