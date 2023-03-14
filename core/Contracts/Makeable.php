<?php

declare(strict_types=1);

namespace Core\Contracts;

/**
 * Use this interface in cases such as the static factory pattern.
 */
interface Makeable
{
    public static function make(string $key): object;
}
