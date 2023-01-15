<?php

declare(strict_types=1);

namespace Tests;

use Amp\PHPUnit\AsyncTestCase;
use Core\App;
use Core\AppProxy;
use Core\Console\Phenix;
use Symfony\Component\Console\Tester\CommandTester;

class TestCase extends AsyncTestCase
{
    protected ?AppProxy $app;

    protected function setUp(): void
    {
        parent::setUp();

        if (! isset($this->app)) {
            $this->app = require __DIR__ . '/../core/bootstrap.php';
            $this->app->enableTestingMode();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (isset($this->app)) {
            $this->app->stop();
            $this->app = null;
        }
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
