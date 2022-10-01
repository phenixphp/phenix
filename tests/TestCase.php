<?php

declare(strict_types=1);

namespace Tests;

use Core\App;
use Core\Console\Phenix;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class TestCase extends BaseTestCase
{
    protected App $app;

    protected function setUp(): void
    {
        $this->app = require_once __DIR__ . '/../core/bootstrap.php';
    }

    protected function tearDown(): void
    {
        $this->app->clearSwaps();
    }

    protected function phenix(string $signature, array $arguments): CommandTester
    {
        $phenix = $this->app::make(Phenix::class);

        $command = $phenix->find($signature);
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);

        return $commandTester;
    }
}
