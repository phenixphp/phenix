<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Contracts\Database\QueryBuilder;
use Core\Database\Constants\Actions;
use Core\Database\Constants\Operators;
use Core\Database\Constants\Order;

class Query implements QueryBuilder
{
    private const PLACEHOLDER = '?';

    protected readonly string $table;
    protected readonly Actions $action;
    protected array $dml;
    protected array $where;
    protected array $fields;
    protected array $arguments;

    public function __construct()
    {
        $this->dml = [];
        $this->where = [];
        $this->fields = [];
        $this->arguments = [];
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function from(string $table): self
    {
        $this->table($table);

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

    // public function where(string $column, Operators $operator, string|int|bool|null $value): self
    // {
    //     $this->pushWhere($column, $operator, $value);

    //     return $this;
    // }

    public function whereEqual(string $column, string|int $value): self
    {
        $this->pushWhere($column, Operators::EQUAL, $value);

        return $this;
    }

    public function whereDistinct(string $column, string|int $value): self
    {
        $this->pushWhere($column, Operators::DISTINCT, $value);

        return $this;
    }

    public function whereGreatherThan(string $column, string|int $value): self
    {
        $this->pushWhere($column, Operators::GREATHER_THAN, $value);

        return $this;
    }

    public function whereGreatherThanOrEqual(string $column, string|int $value): self
    {
        $this->pushWhere($column, Operators::GREATHER_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function whereLessThan(string $column, string|int $value): self
    {
        $this->pushWhere($column, Operators::LESS_THAN, $value);

        return $this;
    }

    public function whereLessThanOrEqual(string $column, string|int $value): self
    {
        $this->pushWhere($column, Operators::LESS_THAN_OR_EQUAL, $value);

        return $this;
    }

    public function whereIn(string $column, array $value): self
    {
        $this->pushWhere($column, Operators::IN, $value);

        return $this;
    }

    public function whereNotIn(string $column, array $value): self
    {
        $this->pushWhere($column, Operators::NOT_IN, $value);

        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->where[] = [$column, Operators::IS_NULL, Operators::AND];

        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->where[] = [$column, Operators::IS_NOT_NULL, Operators::AND];

        return $this;
    }

    public function whereTrue(string $column): self
    {
        $this->where[] = [$column, Operators::IS_TRUE];

        return $this;
    }

    public function whereFalse(string $column): self
    {
        $this->where[] = [$column, Operators::IS_FALSE];

        return $this;
    }

    public function orderBy(string $column, Order $order = Order::DESC)
    {
        # code...
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

    protected function pushWhere(
        string $column,
        Operators $operator,
        array|string|int $value,
        Operators $logicalConnector = Operators::AND,
    ): void {
        $placeholders = \is_array($value)
            ? array_fill(0, count($value), self::PLACEHOLDER)
            : self::PLACEHOLDER;

        $this->where[] = [$column, $operator, $placeholders, $logicalConnector];

        $this->arguments = array_merge($this->arguments, (array) $value);
    }

    protected function buildSelectQuery(): string
    {
        $query = [
            'SELECT',
            $this->implode($this->fields, ', '),
            'FROM',
            $this->table,
        ];

        if (! empty($this->where)) {
            $query[] = 'WHERE';
            $query[] = $this->prepareClausules();
        }

        return $this->implode($query);
    }

    protected function prepareClausules(): array
    {
        $last = array_pop($this->where);

        $this->where[] = array_filter($last, function ($value) {
            return ! \in_array($value, [Operators::AND, Operators::OR], true);
        });

        return array_map(function (array $clausule): array {
            return array_map(function ($value) {
                return match (true) {
                    $value instanceof Operators => $value->value,
                    \is_array($value) => '(' . $this->implode($value, ', ') . ')',
                    default => $value,
                };
            }, $clausule);
        }, $this->where);
    }

    protected function implode(array $statements, string $separator = ' '): string
    {
        $statements = array_map(function ($statement) {
            return \is_array($statement) ? $this->implode($statement) : $statement;
        }, array_filter($statements));

        return implode($separator, $statements);
    }
}
