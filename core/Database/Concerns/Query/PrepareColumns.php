<?php

declare(strict_types=1);

namespace Core\Database\Concerns\Query;

use Core\Database\Functions;
use Core\Database\SelectCase;
use Core\Database\Subquery;
use Core\Exceptions\QueryError;
use Core\Util\Arr;

trait PrepareColumns
{
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
}
