<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\LogicalOperators;
use Core\Database\Constants\Operators;
use Core\Database\Constants\SQL;

trait HasWhereClause
{
    use HasWhereAllClause;
    use HasWhereAnyClause;
    use HasWhereRowClause;
    use HasWhereSomeClause;

    public function whereEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::EQUAL, $value);

        return $this;
    }

    public function orWhereEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereDistinct(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::DISTINCT, $value);

        return $this;
    }

    public function orWhereDistinct(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::DISTINCT, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereGreatherThan(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::GREATHER_THAN, $value);

        return $this;
    }

    public function orWhereGreatherThan(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::GREATHER_THAN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereGreatherThanOrEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::GREATHER_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function orWhereGreatherThanOrEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::GREATHER_THAN_OR_EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereLessThan(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::LESS_THAN, $value);

        return $this;
    }

    public function orWhereLessThan(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::LESS_THAN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereLessThanOrEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::LESS_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function orWhereLessThanOrEqual(string $column, Closure|string|int $value): self
    {
        $this->resolveWhereMethod($column, Operators::LESS_THAN_OR_EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereIn(string $column, Closure|array $value): self
    {
        $this->resolveWhereMethod($column, Operators::IN, $value);

        return $this;
    }

    public function orWhereIn(string $column, Closure|array $value): self
    {
        $this->resolveWhereMethod($column, Operators::IN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereNotIn(string $column, Closure|array $value): self
    {
        $this->resolveWhereMethod($column, Operators::NOT_IN, $value);

        return $this;
    }

    public function orWhereNotIn(string $column, Closure|array $value): self
    {
        $this->resolveWhereMethod($column, Operators::NOT_IN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->pushClause([$column, Operators::IS_NULL]);

        return $this;
    }

    public function orWhereNull(string $column): self
    {
        $this->pushClause([$column, Operators::IS_NULL], LogicalOperators::OR);

        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->pushClause([$column, Operators::IS_NOT_NULL]);

        return $this;
    }

    public function orWhereNotNull(string $column): self
    {
        $this->pushClause([$column, Operators::IS_NOT_NULL], LogicalOperators::OR);

        return $this;
    }

    public function whereTrue(string $column): self
    {
        $this->pushClause([$column, Operators::IS_TRUE]);

        return $this;
    }

    public function orWhereTrue(string $column): self
    {
        $this->pushClause([$column, Operators::IS_TRUE], LogicalOperators::OR);

        return $this;
    }

    public function whereFalse(string $column): self
    {
        $this->pushClause([$column, Operators::IS_FALSE]);

        return $this;
    }

    public function orWhereFalse(string $column): self
    {
        $this->pushClause([$column, Operators::IS_FALSE], LogicalOperators::OR);

        return $this;
    }

    public function whereBetween(string $column, array $values): self
    {
        $this->pushClause([
            $column,
            Operators::BETWEEN,
            SQL::PLACEHOLDER->value,
            LogicalOperators::AND,
            SQL::PLACEHOLDER->value,
        ]);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function orWhereBetween(string $column, array $values): self
    {
        $this->pushClause([
            $column,
            Operators::BETWEEN,
            SQL::PLACEHOLDER->value,
            LogicalOperators::AND,
            SQL::PLACEHOLDER->value,
        ], LogicalOperators::OR);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function whereNotBetween(string $column, array $values): self
    {
        $this->pushClause([
            $column,
            Operators::NOT_BETWEEN,
            SQL::PLACEHOLDER->value,
            LogicalOperators::AND,
            SQL::PLACEHOLDER->value,
        ]);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function orWhereNotBetween(string $column, array $values): self
    {
        $this->pushClause([
            $column,
            Operators::NOT_BETWEEN,
            SQL::PLACEHOLDER->value,
            LogicalOperators::AND,
            SQL::PLACEHOLDER->value,
        ], LogicalOperators::OR);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function whereExists(Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::EXISTS);

        return $this;
    }

    public function orWhereExists(Closure $subquery): self
    {
        $this->whereSubquery(
            subquery: $subquery,
            comparisonOperator: Operators::EXISTS,
            logicalConnector: LogicalOperators::OR
        );

        return $this;
    }

    public function whereNotExists(Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::NOT_EXISTS);

        return $this;
    }

    public function orWhereNotExists(Closure $subquery): self
    {
        $this->whereSubquery(
            subquery: $subquery,
            comparisonOperator: Operators::NOT_EXISTS,
            logicalConnector: LogicalOperators::OR
        );

        return $this;
    }

    public function whereColumn(string $localColumn, string $foreignColumn): self
    {
        $this->pushClause([$localColumn, Operators::EQUAL, $foreignColumn]);

        return $this;
    }
}
