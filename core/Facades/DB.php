<?php

declare(strict_types=1);

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static \Core\Database\QueryBuilder connection(\Core\Database\Constants\Connections $connection)
 * @method static \Core\Database\QueryBuilder table(string $table)
 * @method static \Core\Database\QueryBuilder from(\Closure|string $table)
 * @method static \Core\Database\QueryBuilder select(array $columns)
 * @method static \Core\Database\QueryBuilder selectAllColumns()
 *
 * @see \Core\Database\QueryBuilder
 */
class DB extends Facade
{
    public static function getKeyName(): string
    {
        return \Core\Database\QueryBuilder::class;
    }
}
