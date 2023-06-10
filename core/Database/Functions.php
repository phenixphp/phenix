<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Database\Constants\Functions as FunctionNames;
use Stringable;

class Functions implements Stringable
{
    protected readonly string $alias;

    public function __construct(
        protected readonly FunctionNames $function,
        protected readonly string $column
    ) {
        // ..
    }

    public function as(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function __toString(): string
    {
        $function = $this->function->name . '(' . $this->column . ')';

        if (isset($this->alias)) {
            $function .= ' AS ' . $this->alias;
        }

        return $function;
    }

    public static function avg(string $column): self
    {
        return new self(FunctionNames::AVG, $column);
    }

    public static function sum(string $column): self
    {
        return new self(FunctionNames::SUM, $column);
    }

    public static function min(string $column): self
    {
        return new self(FunctionNames::MIN, $column);
    }

    public static function max(string $column): self
    {
        return new self(FunctionNames::MAX, $column);
    }

    public static function count(string $column): self
    {
        return new self(FunctionNames::COUNT, $column);
    }

    public static function date(string $column): self
    {
        return new self(FunctionNames::DATE, $column);
    }

    public static function month(string $column): self
    {
        return new self(FunctionNames::MONTH, $column);
    }

    public static function year(string $column): self
    {
        return new self(FunctionNames::YEAR, $column);
    }
}
