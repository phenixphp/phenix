<?php

declare(strict_types=1);

namespace Core\Contracts;

interface Buildable
{
    public static function build(): mixed;
}
