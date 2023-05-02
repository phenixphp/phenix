<?php

declare(strict_types=1);

namespace Core\Database;

class Subquery extends Query
{
    protected readonly string $alias;

    // Only allow SELECT

    public function as(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function toSql(): array
    {
        [$dml, $arguments] = parent::toSql();

        if (isset($this->alias)) {
            $dml = "({$dml}) AS {$this->alias}";
        }

        return [$dml, $arguments];
    }

    public static function make(): self
    {
        return new self();
    }
}
