<?php

declare(strict_types=1);

namespace Tests;

use Phenix\Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app?->run();
    }

    protected function tearDown(): void
    {
        $this->app?->stop();

        parent::tearDown();
    }

    protected function getAppDir(): string
    {
        return dirname(__DIR__);
    }

    protected function getEnvFile(): string|null
    {
        $path = $this->getAppDir() . DIRECTORY_SEPARATOR . '.env.testing';

        return file_exists($path) ? 'testing' : null;
    }
}
