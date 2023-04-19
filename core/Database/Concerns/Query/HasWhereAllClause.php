<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\Operators;

trait HasWhereAllClause
{
    public function whereAllEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::EQUAL, $column, Operators::ALL);

        return $this;
    }

    public function whereAllDistinct(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::DISTINCT, $column, Operators::ALL);

        return $this;
    }

    public function whereAllGreatherThan(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::GREATHER_THAN, $column, Operators::ALL);

        return $this;
    }

    public function whereAllGreatherThanOrEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::GREATHER_THAN_OR_EQUAL, $column, Operators::ALL);

        return $this;
    }

    public function whereAllLessThan(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::LESS_THAN, $column, Operators::ALL);

        return $this;
    }

    public function whereAllLessThanOrEqual(string $column, Closure $callback): self
    {
        $this->whereSubquery($callback, Operators::LESS_THAN_OR_EQUAL, $column, Operators::ALL);

        return $this;
    }
}