<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Constants\Joins;
use Core\Database\Join;

trait HasJoinClause
{
    public function innerJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::INNER);

        return $this;
    }

    public function innerJoinOnEqual(string $relationship, string $column, string $value): self
    {
        $this->jointFrom($relationship, $column, $value, Joins::INNER);

        return $this;
    }

    public function leftJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::LEFT);

        return $this;
    }

    public function leftJoinOnEqual(string $relationship, string $column, string $value): self
    {
        $this->jointFrom($relationship, $column, $value, Joins::LEFT);

        return $this;
    }

    public function leftOuterJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::LEFT_OUTER);

        return $this;
    }

    public function rightJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::RIGHT);

        return $this;
    }

    public function rightJoinOnEqual(string $relationship, string $column, string $value): self
    {
        $this->jointFrom($relationship, $column, $value, Joins::RIGHT);

        return $this;
    }

    public function rightOuterJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::RIGHT_OUTER);

        return $this;
    }

    public function crossJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::CROSS);

        return $this;
    }

    protected function jointIt(string $relationship, Closure $callback, Joins $type): void
    {
        $join = new Join($relationship, $type);

        $callback($join);

        $this->pushJoin($join);
    }

    protected function jointFrom(string $relationship, string $column, string $value, Joins $joinType): void
    {
        $join = new Join($relationship, $joinType);
        $join->onEqual($column, $value);

        $this->pushJoin($join);
    }

    protected function pushJoin(Join $join): void
    {
        [$dml, $arguments] = $join->toSql();

        $this->joins[] = $dml;

        $this->arguments = array_merge($this->arguments, $arguments);
    }
}
