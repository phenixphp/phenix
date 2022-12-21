<?php

declare(strict_types=1);

namespace Core\Contracts;

interface Makeable
{
    public static function make(string $key): object;
}
