<?php

declare(strict_types=1);

namespace Core\Contracts;

/**
 * Use this interface to instantiate objects in multiple steps.
 */
interface Buildable
{
    public static function build(): mixed;
}
