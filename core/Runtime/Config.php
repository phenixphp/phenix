<?php

declare(strict_types=1);

namespace Core\Runtime;

use Adbar\Dot;
use Core\Util\Directory;
use InvalidArgumentException;
use SplFixedArray;
use Throwable;

class Config
{
    private Dot $settings;

    public function __construct(array $settings)
    {
        $this->settings = new Dot($settings, true);
    }

    public static function build(): self
    {
        /** @var SplFixedArray<int, string> $paths */
        $paths = SplFixedArray::fromArray(Directory::all(base_path('config')));
        $settings = [];

        foreach ($paths as $path) {
            $key = self::getKey($path);

            $settings[$key] = require $path;
        }

        return new static($settings);
    }

    public function get(string $key): mixed
    {
        try {
            return $this->settings->get($key);
        } catch (Throwable $th) {
            throw new InvalidArgumentException("Invalid configuration key: {$key}");
        }
    }

    public function set(string $key, mixed $value): void
    {
        $this->settings->set($key, $value);
    }

    private static function getKey(string $path): string
    {
        $path = explode(DIRECTORY_SEPARATOR, $path);

        $name = array_pop($path);

        return str_replace('.php', '', $name);
    }
}
