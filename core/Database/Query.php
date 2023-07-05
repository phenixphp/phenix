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
use Core\Database\Constants\SQL;
use Core\Exceptions\QueryError;
use Core\Util\Arr;

class Query extends Clause implements QueryBuilder, Builder
{
    use HasJoinClause;

    protected readonly string $table;
    protected readonly Actions $action;
    protected array $columns;
    protected array $values;
    protected array $joins;
    protected readonly string $having;
    protected readonly array $groupBy;
    protected readonly array $orderBy;
    protected readonly array $limit;
    protected readonly int $rowsCount;
    protected readonly array|string $rows;
    protected bool $ignore = false;
    protected array $uniqueColumns;

    public function __construct()
    {
        $this->ignore = false;

        $this->joins = [];
        $this->columns = [];
        $this->values = [];
        $this->clauses = [];
        $this->arguments = [];
        $this->uniqueColumns = [];
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

    public function select(array $columns): self
    {
        $this->action = Actions::SELECT;

        $this->columns = $columns;

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

        $this->rowsCount = \array_is_list($data) ? count($data) : 1;

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

        $this->rowsCount = \array_is_list($data) ? count($data) : 1;

        return $this;
    }

    public function insertFrom(Closure $subquery, array $columns, bool $ignore = false): self
    {
        $builder = new Subquery();

        $subquery($builder);

        [$dml, $arguments] = $builder->toSql();

        $this->rows = trim($dml, '()');

        $this->arguments = array_merge($this->arguments, $arguments);

        $this->action = Actions::INSERT;

        $this->ignore = $ignore;

        $this->columns = $columns;

        return $this;
    }

    public function update(array $values): self
    {
        $this->action = Actions::UPDATE;

        $this->values = $values;

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
            Actions::UPDATE => $this->buildUpdateSentence(),
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
            $this->prepareColumns($this->columns),
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

    protected function prepareColumns(array $columns): string
    {
        $columns = array_map(function ($column) {
            return match (true) {
                $column instanceof Functions => (string) $column,
                $column instanceof SelectCase => (string) $column,
                $column instanceof Subquery => $this->resolveSubquery($column),
                default => $column,
            };
        }, $columns);

        return Arr::implodeDeeply($columns, ', ');
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

        $this->columns = \array_unique([...$this->columns, ...\array_keys($data)]);

        \sort($this->columns);

        \ksort($data);

        $values = \array_values($data);

        $this->arguments = array_merge($this->arguments, $values);
    }

    private function buildInsertSentence(): string
    {
        $dml = [
            $this->ignore ? 'INSERT IGNORE INTO' : 'INSERT INTO',
            $this->table,
            '(' . Arr::implodeDeeply($this->columns, ', ') . ')',
        ];

        if (isset($this->rows) && \is_string($this->rows)) {
            $dml[] = $this->rows;
        } else {
            $dml[] = 'VALUES';

            $values = [];
            $columnsCount = count($this->columns);

            for ($i = 0; $i < $this->rowsCount; $i++) {
                $placeholders = array_fill(0, $columnsCount, SQL::PLACEHOLDER->value);

                $values[] = '(' . Arr::implodeDeeply($placeholders, ', ') . ')';
            }

            $dml[] = Arr::implodeDeeply($values, ', ');

            if (! empty($this->uniqueColumns)) {
                $dml[] = 'ON DUPLICATE KEY UPDATE';

                $values = [];

                foreach ($this->uniqueColumns as $column) {
                    $values[] = "{$column} = VALUES({$column})";
                }

                $dml[] = Arr::implodeDeeply($values, ', ');
            }
        }

        return Arr::implodeDeeply($dml);
    }

    private function buildUpdateSentence(): string
    {
        $dml = [
            'UPDATE',
            $this->table,
            'SET',
        ];

        $columns = [];
        $arguments = [];

        foreach ($this->values as $column => $value) {
            $arguments[] = $value;

            $columns[] = "{$column} = " . SQL::PLACEHOLDER->value;
        }

        $this->arguments = [...$arguments, ...$this->arguments];

        $dml[] = Arr::implodeDeeply($columns, ', ');

        if (! empty($this->clauses)) {
            $dml[] = 'WHERE';
            $dml[] = $this->prepareClauses($this->clauses);
        }

        return Arr::implodeDeeply($dml);
    }
}
