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

class Query extends Clause implements QueryBuilder, Builder
{
    use HasJoinClause;

    protected readonly string $table;
    protected readonly Actions $action;
    protected array $fields;
    protected array $joins;
    protected readonly string $having;
    protected readonly array $groupBy;
    protected readonly array $orderBy;
    protected readonly array $limit;
    protected array $data;
    protected bool $ignore = false;
    protected readonly array $uniqueColumns;

    public function __construct()
    {
        $this->joins = [];
        $this->clauses = [];
        $this->fields = [];
        $this->arguments = [];
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

    public function insert(array $data): self
    {
        $this->action = Actions::INSERT;

        $this->prepareDataToInsert($data);

        return $this;
    }

    public function insertOrIgnore(array $data): self
    {
        $this->ignore = true;

        $this->insert($data);

        return $this;
    }

    public function upsert(array $data, array $update): self
    {
        $this->action = Actions::INSERT;

        $this->uniqueColumns = $update;

        $this->prepareDataToInsert($data);

        return $this;
    }

    public function groupBy(Functions|array|string $column)
    {
        $column = match (true) {
            $column instanceof Functions => (string) $column,
            default => $column,
        };

        $this->groupBy = [Operators::GROUP_BY->value, Arr::implodeDeeply((array) $column, ', ')];

        return $this;
    }

    public function having(Closure $clause): self
    {
        $having = new Having();

        $clause($having);

        [$dml, $arguments] = $having->toSql();

        $this->having = $dml;

        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    public function orderBy(SelectCase|array|string $column, Order $order = Order::DESC)
    {
        $column = match (true) {
            $column instanceof SelectCase => '(' . $column . ')',
            default => $column,
        };

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
            Actions::INSERT => $this->buildInsertSentence(),
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
            $this->joins,
        ];

        if (! empty($this->clauses)) {
            $query[] = 'WHERE';
            $query[] = $this->prepareClauses($this->clauses);
        }

        if (isset($this->having)) {
            $query[] = $this->having;
        }

        if (isset($this->groupBy)) {
            $query[] = Arr::implodeDeeply($this->groupBy);
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
                $field instanceof Functions => (string) $field,
                $field instanceof SelectCase => (string) $field,
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

    private function prepareDataToInsert(array $data): void
    {
        if (\array_is_list($data)) {
            foreach ($data as $record) {
                $this->prepareDataToInsert($record);
            }

            return;
        }

        $this->fields = \array_unique([...$this->fields, ...\array_keys($data)]);

        \sort($this->fields);

        \ksort($data);

        $this->data[] = array_map(function ($value) {
            return \is_string($value) ? Value::from($value) : $value;
        }, \array_values($data));
    }

    private function buildInsertSentence(): string
    {
        $dml = [
            $this->ignore ? 'INSERT IGNORE INTO' : 'INSERT INTO',
            $this->table,
            '(' . Arr::implodeDeeply($this->fields, ', ') . ')',
            'VALUES',
        ];

        $values = [];

        foreach ($this->data as $record) {
            $values[] = '(' . Arr::implodeDeeply($record, ', ') . ')';
        }

        $dml[] = Arr::implodeDeeply($values, ', ');

        if (isset($this->uniqueColumns)) {
            $dml[] = 'ON DUPLICATE KEY UPDATE';

            foreach ($this->uniqueColumns as $column) {
                $dml[] = "{$column} = VALUES({$column})";
            }
        }

        return Arr::implodeDeeply($dml);
    }
}
