<?php

declare(strict_types=1);

namespace Core\Database;

class Subquery extends QueryGenerator
{
    protected readonly string $alias;

    public function as(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function toSql(): array
    {
        [$dml, $arguments] = parent::toSql();

        if (isset($this->alias)) {
            return ["({$dml}) AS {$this->alias}", $arguments];
        }

        return ["({$dml})", $arguments];
    }

    public static function make(): self
    {
        return new self();
    }
}
