<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\Operators;

trait HasWhereRowClause
{
    public function whereRowEqual(array $columns, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::EQUAL, $this->prepareRowFields($columns));

        return $this;
    }

    public function whereRowDistinct(array $columns, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::DISTINCT, $this->prepareRowFields($columns));

        return $this;
    }

    public function whereRowGreatherThan(array $columns, Closure $subquery): self
    {
        $this->whereSubquery(
            $subquery,
            Operators::GREATHER_THAN,
            $this->prepareRowFields($columns)
        );

        return $this;
    }

    public function whereRowGreatherThanOrEqual(array $columns, Closure $subquery): self
    {
        $this->whereSubquery(
            $subquery,
            Operators::GREATHER_THAN_OR_EQUAL,
            $this->prepareRowFields($columns)
        );

        return $this;
    }

    public function whereRowLessThan(array $columns, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::LESS_THAN, $this->prepareRowFields($columns));

        return $this;
    }

    public function whereRowLessThanOrEqual(array $columns, Closure $subquery): self
    {
        $this->whereSubquery(
            $subquery,
            Operators::LESS_THAN_OR_EQUAL,
            $this->prepareRowFields($columns)
        );

        return $this;
    }

    public function whereRowIn(array $columns, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::IN, $this->prepareRowFields($columns));

        return $this;
    }

    public function whereRowNotIn(array $columns, Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::NOT_IN, $this->prepareRowFields($columns));

        return $this;
    }

    private function prepareRowFields(array $fields)
    {
        return 'ROW(' . $this->prepareFields($fields) . ')';
    }
}
