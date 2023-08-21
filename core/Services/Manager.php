<?php

declare(strict_types=1);

namespace Core\Services;

abstract class Manager
{
    protected array $drivers;

    public function __construct()
    {
        $this->drivers = [];
    }

    abstract public function getDefaultDriver(): string;

    public function driver(string|null $driver = null): object
    {
        $driver ??= $this->getDefaultDriver();

        return $this->drivers[$driver] ??= $this->createDriver($driver);
    }

    protected function createDriver(string $driver): object
    {
        $method = 'create' . ucfirst($driver) . 'Driver';

        return $this->{$method}();
    }
}
