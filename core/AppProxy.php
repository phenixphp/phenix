<?php

declare(strict_types=1);

namespace Core;

use Core\Contracts\App as AppContract;

class AppProxy implements AppContract
{
    private static bool $testingMode = false;

    public function __construct(
        private App $app
    ) {
        // ..
    }

    public function run(): void
    {
        if (self::$testingMode) {
            $this->app->disableSignalTrapping();
        }

        $this->app->run();
    }

    public function stop(): void
    {
        $this->app->stop();
    }

    public function swap(string $key, object $concrete): void
    {
        $this->app->swap($key, $concrete);
    }

    public static function enableTestingMode(): void
    {
        self::$testingMode = true;
    }

    public static function testingModeEnabled(): bool
    {
        return self::$testingMode;
    }
}
