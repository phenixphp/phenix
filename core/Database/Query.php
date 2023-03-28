<?php

declare(strict_types=1);

namespace Core\Database;

use BadMethodCallException;
use Closure;
use Core\Contracts\Database\QueryBuilder;
use Core\Database\Constants\Actions;
use Core\Database\Constants\Operators;
use Core\Database\Constants\Order;
use Stringable;

class Query implements QueryBuilder
{
    private const PLACEHOLDER = '?';

    protected readonly string $table;
    protected readonly Actions $action;
    protected array $where;
    protected array $fields;
    protected array $arguments;
    protected Operators|null $logicalConnector;
    protected readonly array $orderBy;
    protected readonly array $limit;

    public function __construct()
    {
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

    public function whereEqual(string $column, Closure|string|int $value): self
    {
        if ($value instanceof Closure) {
            $this->whereColumnSubquery($column, Operators::EQUAL, $value);
        } else {
        $this->pushWhereWithArgs($column, Operators::EQUAL, $value);
        }

        return $this;
    }

    public function whereDistinct(string $column, Closure|string|int $value): self
    {
        if ($value instanceof Closure) {
            $this->whereColumnSubquery($column, Operators::DISTINCT, $value);
        } else {
        $this->pushWhereWithArgs($column, Operators::DISTINCT, $value);
        }

        return $this;
    }

    public function whereGreatherThan(string $column, Closure|string|int $value): self
    {
        if ($value instanceof Closure) {
            $this->whereColumnSubquery($column, Operators::GREATHER_THAN, $value);
        } else {
        $this->pushWhereWithArgs($column, Operators::GREATHER_THAN, $value);
        }

        return $this;
    }

    public function whereGreatherThanOrEqual(string $column, Closure|string|int $value): self
    {
        if ($value instanceof Closure) {
            $this->whereColumnSubquery($column, Operators::GREATHER_THAN_OR_EQUAL, $value);
        } else {
        $this->pushWhereWithArgs($column, Operators::GREATHER_THAN_OR_EQUAL, $value);
        }

        return $this;
    }

    public function whereLessThan(string $column, Closure|string|int $value): self
    {
        if ($value instanceof Closure) {
            $this->whereColumnSubquery($column, Operators::LESS_THAN, $value);
        } else {
        $this->pushWhereWithArgs($column, Operators::LESS_THAN, $value);
        }

        return $this;
    }

    public function whereLessThanOrEqual(string $column, Closure|string|int $value): self
    {
        if ($value instanceof Closure) {
            $this->whereColumnSubquery($column, Operators::LESS_THAN_OR_EQUAL, $value);
        } else {
        $this->pushWhereWithArgs($column, Operators::LESS_THAN_OR_EQUAL, $value);
        }

        return $this;
    }

    public function whereIn(string $column, array $value): self
    {
        $this->pushWhereWithArgs($column, Operators::IN, $value);

        return $this;
    }

    public function whereNotIn(string $column, array $value): self
    {
        $this->pushWhereWithArgs($column, Operators::NOT_IN, $value);

        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_NULL]);

        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_NOT_NULL]);

        return $this;
    }

    public function whereTrue(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_TRUE]);

        return $this;
    }

    public function whereFalse(string $column): self
    {
        $this->pushWhere([$column, Operators::IS_FALSE]);

        return $this;
    }

    public function whereBetween(string $column, array $values): self
    {
        $this->pushWhere([$column,  Operators::BETWEEN, self::PLACEHOLDER, Operators::AND, self::PLACEHOLDER]);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function whereNotBetween(string $column, array $values): self
    {
        $this->pushWhere([$column,  Operators::NOT_BETWEEN, self::PLACEHOLDER, Operators::AND, self::PLACEHOLDER]);

        $this->arguments = array_merge($this->arguments, (array) $values);

        return $this;
    }

    public function whereExists(Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::EXISTS);

        return $this;
    }

    public function whereNotExists(Closure $subquery): self
    {
        $this->whereSubquery($subquery, Operators::NOT_EXISTS);

        return $this;
    }

    public function orderBy(array|string $column, Order $order = Order::DESC)
    {
        $this->orderBy = [Operators::ORDER_BY->value, $this->implode((array) $column, ', '), $order->value];

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

    public function __call(string $method, array $arguments = [])
    {
        if (str_starts_with($method, 'or')) {
            $method = lcfirst(str_replace('or', '', $method));

            return $this->setLogicalConnector(Operators::OR)
                ->{$method}(...$arguments)
                ->setLogicalConnector(null);
        }

        if (method_exists($this, $method)) {
        return $this->{$method}(...$arguments);
        }

        throw new BadMethodCallException("The method does not exist: {$method}");
    }

    protected function setLogicalConnector(Operators|null $operator): self
    {
        $this->logicalConnector = $operator;

        return $this;
    }

    protected function pushWhereWithArgs(string $column, Operators $operator, array|string|int $value): void
    {
        $placeholders = \is_array($value)
            ? array_fill(0, count($value), self::PLACEHOLDER)
            : self::PLACEHOLDER;

        $this->pushWhere([$column, $operator, $placeholders]);

        $this->arguments = array_merge($this->arguments, (array) $value);
    }

    protected function pushWhere(array $where): void
    {
        if (count($this->where) > 0) {
            array_unshift($where, $this->logicalConnector ?? Operators::AND);
        }

        $this->where[] = $where;
    }

    protected function buildSelectQuery(): string
    {
        $query = [
            'SELECT',
            $this->prepareFields(),
            'FROM',
            $this->table,
        ];

        if (! empty($this->where)) {
            $query[] = 'WHERE';
            $query[] = $this->prepareClauses();
        }

        if (isset($this->orderBy)) {
            $query[] = $this->implode($this->orderBy);
        }

        if (isset($this->limit)) {
            $query[] = $this->implode($this->limit);
        }

        return $this->implode($query);
    }

    private function whereColumnSubquery(string $column, Operators $operator, Closure $subquery): void
    {
        $this->whereSubquery($subquery, $operator, $column);
    }

    private function whereSubquery(Closure $subquery, Operators $operator, string|null $column = null): void
    {
        $builder = new self();

        $subquery($builder);

        [$dml, $arguments] = $builder->toSql();

        $value = '(' . $dml . ')';

        $this->pushWhere(array_filter([$column, $operator, $value]));

        $this->arguments = array_merge($this->arguments, $arguments);
    }

    protected function prepareFields(): string
    {
        $fields = array_map(function ($field) {
            return match (true) {
                $field instanceof Stringable => (string) $field,
                default => $field,
            };
        }, $this->fields);

        return $this->implode($fields, ', ');
    }

    protected function prepareClauses(): array
    {
        return array_map(function (array $clause): array {
            return array_map(function ($value) {
                return match (true) {
                    $value instanceof Operators => $value->value,
                    \is_array($value) => '(' . $this->implode($value, ', ') . ')',
                    default => $value,
                };
            }, $clause);
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
