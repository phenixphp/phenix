<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\Operators;
use Core\Database\Query;

trait HasWhereClause
{
    use HasWhereAnyClause;
    use HasWhereAllClause;
    use HasWhereSomeClause;

    public function whereEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::EQUAL, $value);

        return $this;
    }

    public function whereDistinct(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::DISTINCT, $value);

        return $this;
    }

    public function whereGreatherThan(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::GREATHER_THAN, $value);

        return $this;
    }

    public function whereGreatherThanOrEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::GREATHER_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function whereLessThan(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::LESS_THAN, $value);

        return $this;
    }

    public function whereLessThanOrEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::LESS_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function whereIn(string $column, Closure|array $value): self
    {
        $this->resolveWhereMethod($column, Operators::IN, $value);

        return $this;
    }

    public function whereNotIn(string $column, Closure|array $value): self
    {
        $this->resolveWhereMethod($column, Operators::NOT_IN, $value);

        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_NULL]);

        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_NOT_NULL]);

        return $this;
    }

    public function whereTrue(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_TRUE]);

        return $this;
    }

    public function whereFalse(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_FALSE]);

        return $this;
    }

    public function whereBetween(string $column, array $values): self
    {
        $this->pushWhere([$column,  Operators::BETWEEN, Query::PLACEHOLDER, Operators::AND, Query::PLACEHOLDER]);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function whereNotBetween(string $column, array $values): self
    {
        $this->pushWhere([$column,  Operators::NOT_BETWEEN, Query::PLACEHOLDER, Operators::AND, Query::PLACEHOLDER]);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function whereExists(Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::EXISTS);

        return $this;
    }

    public function whereNotExists(Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::NOT_EXISTS);

        return $this;
    }
}