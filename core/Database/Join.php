<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Contracts\Database\Builder;
use Core\Database\Constants\Joins;
use Core\Database\Constants\LogicalOperators;
use Core\Database\Constants\Operators;
use Core\Util\Arr;

class Join extends Clause implements Builder
{
    public function __construct(
        protected Alias|string $relationship,
        protected readonly Joins $type
    ) {
        $this->clauses = [];
        $this->arguments = [];
    }

    public function onEqual(string $column, string $value): self
    {
        $this->pushClause([$column, Operators::EQUAL, $value]);

        return $this;
    }

    public function orOnEqual(string $column, string $value): self
    {
        $this->pushClause([$column, Operators::EQUAL, $value], LogicalOperators::OR);

        return $this;
    }

    public function onDistinct(string $column, string $value): self
    {
        $this->pushClause([$column, Operators::DISTINCT, $value]);

        return $this;
    }

    public function orOnDistinct(string $column, string $value): self
    {
        $this->pushClause([$column, Operators::DISTINCT, $value], LogicalOperators::OR);

        return $this;
    }

    public function toSql(): array
    {
        $clauses = Arr::implodeDeeply($this->prepareClauses($this->clauses));

        return [
            "{$this->type->value} {$this->relationship} ON {$clauses}",
            $this->arguments,
        ];
    }
}
