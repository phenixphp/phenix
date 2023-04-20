<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\Operators;

trait HasWhereAllClause
{
    public function whereAllEqual(string $column, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::EQUAL, $column, Operators::ALL);

        return $this;
    }

    public function whereAllDistinct(string $column, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::DISTINCT, $column, Operators::ALL);

        return $this;
    }

    public function whereAllGreatherThan(string $column, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::GREATHER_THAN, $column, Operators::ALL);

        return $this;
    }

    public function whereAllGreatherThanOrEqual(string $column, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::GREATHER_THAN_OR_EQUAL, $column, Operators::ALL);

        return $this;
    }

    public function whereAllLessThan(string $column, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::LESS_THAN, $column, Operators::ALL);

        return $this;
    }

    public function whereAllLessThanOrEqual(string $column, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::LESS_THAN_OR_EQUAL, $column, Operators::ALL);

        return $this;
    }
}
