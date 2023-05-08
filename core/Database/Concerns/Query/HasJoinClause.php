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

    public function rightJoin(string $relationship, Closure $callback): self
    {
        $this->jointIt($relationship, $callback, Joins::RIGHT);

        return $this;
    }

    protected function jointIt(string $relationship, Closure $callback, Joins $type): void
    {
        $join = new Join($relationship, $type);

        $callback($join);

        $this->joins[] = (string) $join;
    }
}