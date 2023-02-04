<?php

declare(strict_types=1);

namespace Core\Contracts;

interface Arrayable
{
    public function toArray(): array;
}
