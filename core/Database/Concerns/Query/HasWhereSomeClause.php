<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\Operators;

trait HasWhereSomeClause
{
    public function whereSomeEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::EQUAL, $column, Operators::SOME);

        return $this;
    }

    public function whereSomeDistinct(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::DISTINCT, $column, Operators::SOME);

        return $this;
    }

    public function whereSomeGreatherThan(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::GREATHER_THAN, $column, Operators::SOME);

        return $this;
    }

    public function whereSomeGreatherThanOrEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::GREATHER_THAN_OR_EQUAL, $column, Operators::SOME);

        return $this;
    }

    public function whereSomeLessThan(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::LESS_THAN, $column, Operators::SOME);

        return $this;
    }

    public function whereSomeLessThanOrEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::LESS_THAN_OR_EQUAL, $column, Operators::SOME);

        return $this;
    }
}