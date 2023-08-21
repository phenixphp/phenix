<?php

declare(strict_types=1);

namespace Core\Database;

use Stringable;

class Alias implements Stringable
{
    protected string $alias;

    public function __construct(protected readonly string $name)
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
        return "{$this->name} AS {$this->alias}";
    }

    public static function of(string $name): self
    {
        return new self($name);
    }
}
