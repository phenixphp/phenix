<?php

declare(strict_types=1);

namespace Core\Database;

use Closure;
use Core\Contracts\Database\Builder;
use Core\Database\Concerns\Query\HasWhereClause;
use Core\Database\Concerns\Query\PrepareColumns;
use Core\Database\Constants\LogicalOperators;
use Core\Database\Constants\Operators;
use Core\Database\Constants\SQL;
use Core\Util\Arr;

abstract class Clause implements Builder
{
    use HasWhereClause;
    use PrepareColumns;

    protected array $clauses;
    protected array $arguments;

    protected function resolveWhereMethod(
        string $column,
        Operators $operator,
        Closure|array|string|int $value,
        LogicalOperators $logicalConnector = LogicalOperators::AND
    ): void {
        if ($value instanceof Closure) {
            $this->whereSubquery(
                subquery: $value,
                comparisonOperator: $operator,
                column: $column,
                logicalConnector: $logicalConnector
            );
        } else {
            $this->pushWhereWithArgs($column, $operator, $value, $logicalConnector);
        }
    }

    protected function whereSubquery(
        Closure $subquery,
        Operators $comparisonOperator,
        string|null $column = null,
        Operators|null $operator = null,
        LogicalOperators $logicalConnector = LogicalOperators::AND
    ): void {
        $builder = new Subquery();

        $subquery($builder);

        [$dml, $arguments] = $builder->toSql();

        $value = $operator?->value . $dml;

        $this->pushClause(array_filter([$column, $comparisonOperator, $value]), $logicalConnector);

        $this->arguments = array_merge($this->arguments, $arguments);
    }

    protected function pushWhereWithArgs(
        string $column,
        Operators $operator,
        array|string|int $value,
        LogicalOperators $logicalConnector = LogicalOperators::AND
    ): void {
        $placeholders = \is_array($value)
            ? array_fill(0, count($value), SQL::PLACEHOLDER->value)
            : SQL::PLACEHOLDER->value;

        $this->pushClause([$column, $operator, $placeholders], $logicalConnector);

        $this->arguments = array_merge($this->arguments, (array) $value);
    }

    protected function pushClause(array $where, LogicalOperators $logicalConnector = LogicalOperators::AND): void
    {
        if (count($this->clauses) > 0) {
            array_unshift($where, $logicalConnector);
        }

        $this->clauses[] = $where;
    }

    protected function prepareClauses(array $clauses): array
    {
        return array_map(function (array $clause): array {
            return array_map(function ($value) {
                return match (true) {
                    $value instanceof Operators => $value->value,
                    $value instanceof LogicalOperators => $value->value,
                    \is_array($value) => '(' . Arr::implodeDeeply($value, ', ') . ')',
                    default => $value,
                };
            }, $clause);
        }, $clauses);
    }
}
