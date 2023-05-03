<?php

declare(strict_types=1);

namespace Core\Database;

use Stringable;

class Alias implements Stringable
{
    protected readonly string $alias;

    public function __construct(protected readonly string $column)
    {
        // ..
    }

    public function as(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function __toString(): string
    {
        return "{$this->column} AS {$this->alias}";
    }

    public static function of(string $column): self
    {
        return new self($column);
    }
}
