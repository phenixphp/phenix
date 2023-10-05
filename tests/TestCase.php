<?php

declare(strict_types=1);

namespace Tests;

use Amp\PHPUnit\AsyncTestCase;
use Phenix\App;
use Phenix\AppBuilder;
use Phenix\AppProxy;
use Phenix\Console\Phenix;
use Symfony\Component\Console\Tester\CommandTester;

class TestCase extends AsyncTestCase
{
    protected ?AppProxy $app;

    protected function setUp(): void
    {
        parent::setUp();

        if (! isset($this->app)) {
            $this->app = AppBuilder::build(dirname(__DIR__), 'testing');
            $this->app->enableTestingMode();
            $this->app->run();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->app?->stop();
        $this->app = null;
    }

    protected function phenix(string $signature, array $arguments): CommandTester
    {
        $phenix = App::make(Phenix::class);

        $command = $phenix->find($signature);
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);

        return $commandTester;
    }
}
