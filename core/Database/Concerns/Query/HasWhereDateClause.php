<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Carbon\CarbonInterface;
use Core\Database\Constants\LogicalOperators;
use Core\Database\Constants\Operators;
use Core\Database\Functions;

trait HasWhereDateClause
{
    public function whereDateEqual(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::EQUAL, $value);

        return $this;
    }

    public function orWhereDateEqual(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereDateGreatherThan(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::GREATHER_THAN, $value);

        return $this;
    }

    public function orWhereDateGreatherThan(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::GREATHER_THAN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereDateGreatherThanOrEqual(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::GREATHER_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function orWhereDateGreatherThanOrEqual(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::GREATHER_THAN_OR_EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereDateLessThan(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::LESS_THAN, $value);

        return $this;
    }

    public function orWhereDateLessThan(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::LESS_THAN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereDateLessThanOrEqual(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::LESS_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function orWhereDateLessThanOrEqual(string $column, CarbonInterface|string $value): self
    {
        $this->pushDateClause($column, Operators::LESS_THAN_OR_EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereMonthEqual(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::EQUAL, $value);

        return $this;
    }

    public function orWhereMonthEqual(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereMonthGreatherThan(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::GREATHER_THAN, $value);

        return $this;
    }

    public function orWhereMonthGreatherThan(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::GREATHER_THAN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereMonthGreatherThanOrEqual(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::GREATHER_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function orWhereMonthGreatherThanOrEqual(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::GREATHER_THAN_OR_EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereMonthLessThan(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::LESS_THAN, $value);

        return $this;
    }

    public function orWhereMonthLessThan(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::LESS_THAN, $value, LogicalOperators::OR);

        return $this;
    }

    public function whereMonthLessThanOrEqual(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::LESS_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function orWhereMonthLessThanOrEqual(string $column, CarbonInterface|int $value): self
    {
        $this->pushMonthClause($column, Operators::LESS_THAN_OR_EQUAL, $value, LogicalOperators::OR);

        return $this;
    }

    protected function pushDateClause(
        string $column,
        Operators $operator,
        CarbonInterface|string $value,
        LogicalOperators $logicalConnector = LogicalOperators::AND
    ): void {
        if ($value instanceof CarbonInterface) {
            $value = $value->format('Y-m-d');
        }

        $this->pushTimeClause(
            Functions::date($column),
            $operator,
            $value,
            $logicalConnector
        );
    }

    protected function pushMonthClause(
        string $column,
        Operators $operator,
        CarbonInterface|int $value,
        LogicalOperators $logicalConnector = LogicalOperators::AND
    ): void {
        if ($value instanceof CarbonInterface) {
            $value = (int) $value->format('m');
        }

        $this->pushTimeClause(
            Functions::month($column),
            $operator,
            $value,
            $logicalConnector
        );
    }

    protected function pushTimeClause(
        Functions $function,
        Operators $operator,
        CarbonInterface|string|int $value,
        LogicalOperators $logicalConnector = LogicalOperators::AND
    ): void {
        $this->pushWhereWithArgs((string) $function, $operator, $value, $logicalConnector);
    }
}
