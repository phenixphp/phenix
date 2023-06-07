<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Contracts\Database\Builder;
use Core\Database\Constants\Joins;
use Core\Database\Constants\LogicalOperators;
use Core\Database\Constants\Operators;
use Core\Database\Constants\SQL;
use Core\Util\Arr;

class Join implements Builder
{
    /**
     * @var array<int, array<int, string|\Core\Database\Constants\Operators>>
     */
    protected array $clauses;

    /**
     * @var array<int, string|int>
     */
    protected array $arguments;

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

    public function whereEqual(string $column, string|int $value): self
    {
        $this->pushClause([$column, Operators::EQUAL, SQL::PLACEHOLDER->value]);

        $this->arguments = array_merge($this->arguments, (array) $value);

        return $this;
    }

    public function orWhereEqual(string $column, string|int $value): self
    {
        $this->pushClause([$column, Operators::EQUAL, SQL::PLACEHOLDER->value], LogicalOperators::OR);

        $this->arguments = array_merge($this->arguments, (array) $value);

        return $this;
    }

    public function toSql(): array
    {
        $clauses = Arr::implodeDeeply($this->prepareClauses());

        return [
            "{$this->type->value} {$this->relationship} ON {$clauses}",
            $this->arguments,
        ];
    }

    /**
     * @param array<int, string|\Core\Database\Constants\Operators> $clause
     * @param \Core\Database\Constants\LogicalOperators $logicalConnector
     * @return void
     */
    protected function pushClause(array $clause, LogicalOperators $logicalConnector = LogicalOperators::AND): void
    {
        if (! empty($this->clauses)) {
            array_unshift($clause, $logicalConnector);
        }

        $this->clauses[] = $clause;
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function prepareClauses(): array
    {
        return array_map(function (array $clause): array {
            return array_map(function ($value) {
                return match (true) {
                    $value instanceof Operators => $value->value,
                    $value instanceof LogicalOperators => $value->value,
                    default => $value,
                };
            }, $clause);
        }, $this->clauses);
    }
}
