<?php

declare(strict_types=1);

namespace Core\Contracts\Database;

interface QueryBuilder
{
    public function select(array $fields): self;
}
