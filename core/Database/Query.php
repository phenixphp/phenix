<?php

declare(strict_types=1);

namespace Core\Database;

use Closure;
use Core\Contracts\Database\Builder;
use Core\Contracts\Database\QueryBuilder;
use Core\Database\Concerns\Query\HasJoinClause;
use Core\Database\Constants\Actions;
use Core\Database\Constants\Operators;
use Core\Database\Constants\Order;
use Core\Exceptions\QueryError;
use Core\Util\Arr;
use Stringable;

class Query extends Clause implements QueryBuilder, Builder
{
    use HasJoinClause;

    protected readonly string $table;
    protected readonly Actions $action;
    protected array $fields;
    protected array $joins;
    protected readonly array $orderBy;
    protected readonly array $limit;

    public function __construct()
    {
        $this->joins = [];
        $this->where = [];
        $this->fields = [];
        $this->arguments = [];
        $this->logicalConnector = null;
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function from(Closure|string $table): self
    {
        if ($table instanceof Closure) {
            $builder = new Subquery();

            $table($builder);

            [$dml, $arguments] = $builder->toSql();

            $this->table($dml);

            $this->arguments = array_merge($this->arguments, $arguments);

        } else {
            $this->table($table);
        }

        return $this;
    }

    public function select(array $fields): self
    {
        $this->action = Actions::SELECT;

        $this->fields = $fields;

        return $this;
    }

    public function selectAllColumns(): self
    {
        $this->select(['*']);

        return $this;
    }

    public function orderBy(array|string $column, Order $order = Order::DESC)
    {
        $this->orderBy = [Operators::ORDER_BY->value, Arr::implodeDeeply((array) $column, ', '), $order->value];

        return $this;
    }

    public function limit(int $number): self
    {
        $this->limit = [Operators::LIMIT->value, abs($number)];

        return $this;
    }

    public function first(): self
    {
        $this->limit(1);

        return $this;
    }

    public function toSql(): array
    {
        $sql = match ($this->action) {
            Actions::SELECT => $this->buildSelectQuery(),
        };

        return [
            $sql,
            $this->arguments,
        ];
    }

    protected function buildSelectQuery(): string
    {
        $query = [
            'SELECT',
            $this->prepareFields($this->fields),
            'FROM',
            $this->table,
        ];

        $query[] = $this->joins;

        if (! empty($this->where)) {
            $query[] = 'WHERE';
            $query[] = $this->prepareClauses($this->where);
        }

        if (isset($this->orderBy)) {
            $query[] = Arr::implodeDeeply($this->orderBy);
        }

        if (isset($this->limit)) {
            $query[] = Arr::implodeDeeply($this->limit);
        }

        return Arr::implodeDeeply($query);
    }

    protected function prepareFields(array $fields): string
    {
        $fields = array_map(function ($field) {
            return match (true) {
                $field instanceof Stringable => (string) $field,
                $field instanceof Subquery => $this->resolveSubquery($field),
                default => $field,
            };
        }, $fields);

        return Arr::implodeDeeply($fields, ', ');
    }

    private function resolveSubquery(Subquery $subquery): string
    {
        [$dml, $arguments] = $subquery->toSql();

        if (! str_contains($dml, 'LIMIT 1')) {
            throw new QueryError('The subquery must be limited to one record');
        }

        $this->arguments = array_merge($this->arguments, $arguments);

        return $dml;
    }
}
