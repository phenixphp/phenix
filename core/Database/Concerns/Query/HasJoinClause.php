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

    public function leftJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::LEFT);

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

        [$dml, $arguments] = $join->toSql();

        $this->joins[] = $dml;

        $this->arguments = array_merge($this->arguments, $arguments);
    }
}
