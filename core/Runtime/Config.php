<?php

declare(strict_types=1);

namespace Core\Runtime;

use Adbar\Dot;
use Core\Util\Directory;
use SplFixedArray;

use function Core\base_path;

class Config
{
    /**
     * @var Dot<string, array|string|int|bool>
     */
    private Dot $settings;

    /**
     * @param array<string, array|string|int|bool> $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = new Dot($settings, true);
    }

    public static function build(): self
    {
        /** @var SplFixedArray<string> $paths */
        $paths = SplFixedArray::fromArray(Directory::all(base_path('config')));
        $settings = [];

        foreach ($paths as $path) {
            $key = self::getKey($path);

            $settings[$key] = require $path;
        }

        return new self($settings);
    }

    public function get(string $key): mixed
    {
        return $this->settings->get($key);
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
