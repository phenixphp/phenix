<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\Operators;

trait HasWhereAnyClause
{
    public function whereAnyEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::EQUAL, $column, Operators::ANY);

        return $this;
    }

    public function whereAnyDistinct(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::DISTINCT, $column, Operators::ANY);

        return $this;
    }

    public function whereAnyGreatherThan(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::GREATHER_THAN, $column, Operators::ANY);

        return $this;
    }

    public function whereAnyGreatherThanOrEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::GREATHER_THAN_OR_EQUAL, $column, Operators::ANY);

        return $this;
    }

    public function whereAnyLessThan(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::LESS_THAN, $column, Operators::ANY);

        return $this;
    }

    public function whereAnyLessThanOrEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::LESS_THAN_OR_EQUAL, $column, Operators::ANY);

        return $this;
    }
}