<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Closure;
use Core\Database\Alias;
use Core\Database\Constants\Actions;
use Core\Database\Constants\Operators;
use Core\Database\Constants\Order;
use Core\Database\Constants\SQL;
use Core\Database\Functions;
use Core\Database\Having;
use Core\Database\SelectCase;
use Core\Database\Subquery;
use Core\Util\Arr;

trait BuildsQuery
{
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

        return $this;
    }

    public function insertOrIgnore(array $values): self
    {
        $this->ignore = true;

        $this->insert($values);

        return $this;
    }

    public function upsert(array $values, array $columns): self
    {
        $this->action = Actions::INSERT;

        $this->uniqueColumns = $columns;

        $this->prepareDataToInsert($values);

        return $this;
    }

    public function insertFrom(Closure $subquery, array $columns, bool $ignore = false): self
    {
        $builder = new Subquery();

        $subquery($builder);

        [$dml, $arguments] = $builder->toSql();

        $this->rawStatement = trim($dml, '()');

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

    public function delete(): self
    {
        $this->action = Actions::DELETE;

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

    public function page(int $page = 1, int $perPage = 15): self
    {
        $this->limit($perPage);

        $page = $page < 1 ? 1 : $page;

        $offset = $page === 1 ? 0 : (($page - 1) * abs($perPage));

        $this->offset = [Operators::OFFSET->value, $offset];

        return $this;
    }

    public function count(string $column = '*'): self
    {
        $this->action = Actions::SELECT;

        $this->columns = [Functions::count($column)];

        return $this;
    }

    public function exists(): self
    {
        $this->action = Actions::SELECT_EXISTS;

        return $this;
    }

    public function toSql(): array
    {
        $sql = match ($this->action) {
            Actions::SELECT => $this->buildSelectQuery(),
            Actions::SELECT_EXISTS => $this->buildSelectExistsQuery(),
            Actions::INSERT => $this->buildInsertSentence(),
            Actions::UPDATE => $this->buildUpdateSentence(),
            Actions::DELETE => $this->buildDeleteSentence(),
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

        if (isset($this->offset)) {
            $query[] = Arr::implodeDeeply($this->offset);

        }

        return Arr::implodeDeeply($query);
    }

    protected function buildSelectExistsQuery(): string
    {
        $query = ['SELECT EXISTS'];

        $subquery[] = "SELECT 1 FROM {$this->table}";

        if (! empty($this->clauses)) {
            $subquery[] = 'WHERE';
            $subquery[] = $this->prepareClauses($this->clauses);
        }

        $query[] = Alias::of('(' . Arr::implodeDeeply($subquery) . ')')->as('exists');

        return Arr::implodeDeeply($query);
    }

    private function prepareDataToInsert(array $data): void
    {
        if (\array_is_list($data)) {
            foreach ($data as $record) {
                $this->prepareDataToInsert($record);
            }

            return;
        }

        \ksort($data);

        $this->columns = \array_unique([...$this->columns, ...\array_keys($data)]);

        $this->arguments = \array_merge($this->arguments, \array_values($data));

        $this->values[] = array_fill(0, count($data), SQL::PLACEHOLDER->value);
    }

    private function buildInsertSentence(): string
    {
        $dml = [
            $this->ignore ? 'INSERT IGNORE INTO' : 'INSERT INTO',
            $this->table,
            '(' . Arr::implodeDeeply($this->columns, ', ') . ')',
        ];

        if (isset($this->rawStatement)) {
            $dml[] = $this->rawStatement;
        } else {
            $dml[] = 'VALUES';

            $placeholders = array_map(function (array $value): string {
                return '(' . Arr::implodeDeeply($value, ', ') . ')';
            }, $this->values);

            $dml[] = Arr::implodeDeeply($placeholders, ', ');

            if (! empty($this->uniqueColumns)) {
                $dml[] = 'ON DUPLICATE KEY UPDATE';

                $columns = array_map(function (string $column): string {
                    return "{$column} = VALUES({$column})";
                }, $this->uniqueColumns);

                $dml[] = Arr::implodeDeeply($columns, ', ');
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

    private function buildDeleteSentence(): string
    {
        $dml = [
            'DELETE FROM',
            $this->table,
        ];

        if (! empty($this->clauses)) {
            $dml[] = 'WHERE';
            $dml[] = $this->prepareClauses($this->clauses);
        }

        return Arr::implodeDeeply($dml);
    }
}
