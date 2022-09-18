<?php

namespace Tests\Unit\Concerns;

use Core\Console\Phenix;
use Symfony\Component\Console\Tester\CommandTester;

trait InteractWithConsole
{
    public function call(string $signature, array $arguments): CommandTester
    {
        $application = new Phenix();
        $application->loadCommands();

        $command = $application->find($signature);
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);

        return $commandTester;
    }
}
