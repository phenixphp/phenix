<?php

declare(strict_types=1);

namespace Tests;

use Amp\Cancellation;
use Amp\Sync\Channel;
use Phenix\Tasks\Result;
use Phenix\Tasks\Task;
use Phenix\Testing\TestCase as BaseTestCase;
use ReflectionMethod;
use Tests\Internal\FakeCancellation;
use Tests\Internal\FakeChannel;

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

    protected function getFakeChannel(): Channel
    {
        return new FakeChannel();
    }

    protected function getFakeCancellation(): Cancellation
    {
        return new FakeCancellation();
    }

    protected function runTask(Task $task): Result
    {
        $handle = new ReflectionMethod($task, 'handle');

        /** @var Result $result */
        $result = $handle->invoke($task, $this->getFakeChannel(), $this->getFakeCancellation());

        return $result;
    }
}
