<?php

declare(strict_types=1);

namespace Core\Contracts\Database;

interface Builder
{
    public function toSql(): array;
}
