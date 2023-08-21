<?php

declare(strict_types=1);

namespace Core\Database\ORM;

abstract class Ashes
{
    protected string $table;

    public function getTable(): string
    {
        return $this->table;
    }
}
