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
        protected readonly string $field
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
        $function = $this->function->name . '(' . $this->field . ')';

        if (isset($this->alias)) {
            $function .= ' AS ' . $this->alias;
        }

        return $function;
    }

    public static function avg(string $field): self
    {
        return new self(FunctionNames::AVG, $field);
    }

    public static function sum(string $field): self
    {
        return new self(FunctionNames::SUM, $field);
    }

    public static function min(string $field): self
    {
        return new self(FunctionNames::MIN, $field);
    }

    public static function max(string $field): self
    {
        return new self(FunctionNames::MAX, $field);
    }
}
