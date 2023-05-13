<?php

declare(strict_types=1);

namespace Core\Database;

use Closure;
use Core\Contracts\Database\Builder;
use Core\Database\Concerns\Query\HasWhereClause;
use Core\Database\Constants\Operators;
use Core\Database\Constants\SQL;
use Core\Util\Arr;

abstract class Clause implements Builder
{
    use HasWhereClause;

    protected array $where;
    protected array $arguments;
    protected Operators|null $logicalConnector;

    protected function resolveWhereMethod(string $column, Operators $operator, Closure|array|string|int $value): void
    {
        if ($value instanceof Closure) {
            $this->whereSubquery($value, $operator, $column);
        } else {
            $this->pushWhereWithArgs($column, $operator, $value);
        }
    }

    protected function whereSubquery(
        Closure $subquery,
        Operators $comparisonOperator,
        string|null $column = null,
        Operators|null $operator = null
    ): void {
        $builder = new Subquery();

        $subquery($builder);

        [$dml, $arguments] = $builder->toSql();

        $value = $operator?->value . $dml;

        $this->pushClause(array_filter([$column, $comparisonOperator, $value]));

        $this->arguments = array_merge($this->arguments, $arguments);
    }

    protected function pushWhereWithArgs(string $column, Operators $operator, array|string|int $value): void
    {
        $placeholders = \is_array($value)
            ? array_fill(0, count($value), SQL::PLACEHOLDER->value)
            : SQL::PLACEHOLDER->value;

        $this->pushClause([$column, $operator, $placeholders]);

        $this->arguments = array_merge($this->arguments, (array) $value);
    }

    protected function pushClause(array $where): void
    {
        if (count($this->where) > 0) {
            array_unshift($where, $this->logicalConnector ?? Operators::AND);
        }

        $this->where[] = $where;
    }

    protected function prepareClauses(array $clauses): array
    {
        return array_map(function (array $clause): array {
            return array_map(function ($value) {
                return match (true) {
                    $value instanceof Operators => $value->value,
                    \is_array($value) => '(' . Arr::implodeDeeply($value, ', ') . ')',
                    default => $value,
                };
            }, $clause);
        }, $clauses);
    }

    protected function setLogicalConnector(Operators|null $operator): self
    {
        $this->logicalConnector = $operator;

        return $this;
    }
}
