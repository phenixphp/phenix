<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Database\Constants\Joins;
use Core\Database\Constants\Operators;
use Core\Util\Arr;
use Stringable;

class Join implements Stringable
{
    /**
     * @var array<int, array<int, string|\Core\Database\Constants\Operators>>
     */
    protected array $clauses;

    public function __construct(
        protected Alias|string $relationship,
        protected Joins $type
    ) {
        $this->clauses = [];
    }

    public function onEqual(string $leftColumn, string $rightColumn): self
    {
        $this->pushClause([$leftColumn, Operators::EQUAL, $rightColumn]);

        return $this;
    }

    public function orOnEqual(string $leftColumn, string $rightColumn): self
    {
        $this->pushClause([$leftColumn, Operators::EQUAL, $rightColumn, Operators::OR]);

        return $this;
    }

    public function __toString(): string
    {
        if ($this->relationship instanceof Alias) {
            $this->relationship = (string) $this->relationship;
        }

        $clauses = Arr::implodeDeeply($this->prepareClauses());

        return "{$this->type->value} {$this->relationship} ON {$clauses}";
    }

    /**
     * @param array<int, string|\Core\Database\Constants\Operators> $clause
     * @param \Core\Database\Constants\Operators $logicalConnector
     * @return void
     */
    protected function pushClause(array $clause, Operators $logicalConnector = Operators::AND): void
    {
        if (!empty($this->clauses)) {
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
                    default => $value,
                };
            }, $clause);
        }, $this->clauses);
    }
}
