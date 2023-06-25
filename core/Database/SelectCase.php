<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Database\Constants\Operators;
use Core\Util\Arr;
use Stringable;

class SelectCase implements Stringable
{
    protected array $cases;
    protected readonly Value|string $default;
    protected readonly string $alias;

    public function __construct()
    {
        $this->cases = [];
    }

    public function whenEqual(string $column, Value|string $value, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::EQUAL,
            $result,
            $value
        );

        return $this;
    }

    public function whenDistinct(string $column, Value|string $value, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::DISTINCT,
            $result,
            $value
        );

        return $this;
    }

    public function whenGreatherThan(string $column, Value|string $value, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::GREATHER_THAN,
            $result,
            $value
        );

        return $this;
    }

    public function whenGreatherThanOrEqual(string $column, Value|string $value, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::GREATHER_THAN_OR_EQUAL,
            $result,
            $value
        );

        return $this;
    }

    public function whenLessThan(string $column, Value|string $value, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::LESS_THAN,
            $result,
            $value
        );

        return $this;
    }

    public function whenLessThanOrEqual(string $column, Value|string $value, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::LESS_THAN_OR_EQUAL,
            $result,
            $value
        );

        return $this;
    }

    public function whenNull(string $column, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::IS_NULL,
            $result
        );

        return $this;
    }

    public function whenNotNull(string $column, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::IS_NOT_NULL,
            $result
        );

        return $this;
    }

    public function whenTrue(string $column, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::IS_TRUE,
            $result
        );

        return $this;
    }

    public function whenFalse(string $column, Value|string $result): self
    {
        $this->pushCase(
            $column,
            Operators::IS_FALSE,
            $result
        );

        return $this;
    }

    public function defaultResult(Value|string $value): self
    {
        $this->default = $value;

        return $this;
    }

    public function as(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function __toString(): string
    {
        $cases = array_map(function (array $case): array {
            return array_map(function (Operators|string|int $item): string|int {
                return match (true) {
                    $item instanceof Operators => $item->value,
                    default => (string) $item,
                };
            }, $case);
        }, $this->cases);

        if (isset($this->default)) {
            $cases[] = ['ELSE ' . strval($this->default)];
        }

        $cases[] = 'END';

        $dml = 'CASE ' . Arr::implodeDeeply($cases);

        if (isset($this->alias)) {
            $dml = '(' . $dml . ') AS ' . $this->alias;
        }

        return $dml;
    }

    protected function pushCase(
        string $column,
        Operators $operators,
        Value|string $result,
        Value|string|null $value = null
    ): void {
        $condition = array_filter([$column, $operators, $value]);

        $this->cases[] = ['WHEN', ...$condition, 'THEN', $result];
    }
}
