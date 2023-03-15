<?php

declare(strict_types=1);

namespace Tests\Core\Database;

use Core\Database\Constants\Operators;
use Core\Database\Query;

it('generates query to select all columns of table', function () {
    $query = new Query();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users');
    expect($params)->toBeEmpty($params);
});

it('generates query to select all columns from table', function () {
    $query = new Query();

    $sql = $query->selectAllColumns()
        ->from('users')
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users');
    expect($params)->toBeEmpty($params);
});

it('generates query to select a record by column', function () {
    $query = new Query();

    $sql = $query->table('users')
        ->whereEqual('id', 1)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users WHERE id = ?');
    expect($params)->toBe([1]);
});

it('generates query to select a record using many clausules', function () {
    $query = new Query();

    $sql = $query->table('users')
        ->whereEqual('username', 'john')
        ->whereEqual('email', 'john@mail.com')
        ->whereEqual('document', 123456)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users WHERE username = ? AND email = ? AND document = ?');
    expect($params)->toBe(['john', 'john@mail.com', 123456]);
});

it('generates query to select using comparison clausules', function (
    string $method,
    string $column,
    string $operator,
    string|int $value
) {
    $query = new Query();

    $sql = $query->table('users')
        ->{$method}($column, $value)
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE {$column} {$operator} ?");
    expect($params)->toBe([$value]);
})->with([
    ['whereDistinct', 'id', Operators::DISTINCT->value, 1],
    ['whereGreatherThan', 'id', Operators::GREATHER_THAN->value, 1],
    ['whereGreatherThanOrEqual', 'id', Operators::GREATHER_THAN_OR_EQUAL->value, 1],
    ['whereLessThan', 'id', Operators::LESS_THAN->value, 1],
    ['whereLessThanOrEqual', 'id', Operators::LESS_THAN_OR_EQUAL->value, 1],
]);

it('generates query selecting specific columns', function () {
    $query = new Query();

    $sql = $query->table('users')
        ->whereEqual('id', 1)
        ->select(['id', 'name', 'email'])
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT id, name, email FROM users WHERE id = ?');
    expect($params)->toBe([1]);
});


it('generates query using in and not in operators', function (string $method, string $operator) {
    $query = new Query();

    $sql = $query->table('users')
        ->{$method}('id', [1, 2, 3])
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE id {$operator} (?, ?, ?)");
    expect($params)->toBe([1, 2, 3]);
})->with([
    ['whereIn', Operators::IN->value],
    ['whereNotIn', Operators::NOT_IN->value],
]);

it('generates query to select null or not null columns', function (string $method, string $operator) {
    $query = new Query();

    $sql = $query->table('users')
        ->{$method}('verified_at')
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE verified_at {$operator}");
    expect($params)->toBe([]);
})->with([
    ['whereNull', Operators::IS_NULL->value],
    ['whereNotNull', Operators::IS_NOT_NULL->value],
]);

it('generates query to select boolean columns', function (string $method, string $operator) {
    $query = new Query();

    $sql = $query->table('users')
        ->{$method}('enabled')
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE enabled {$operator}");
    expect($params)->toBe([]);
})->with([
    ['whereTrue', Operators::IS_TRUE->value],
    ['whereFalse', Operators::IS_FALSE->value],
]);

it('generates query using logical connectors', function () {
    $query = new Query();

    $date = date('Y-m-d');

    $sql = $query->table('users')
        ->whereNotNull('verified_at')
        ->whereGreatherThan('created_at', $date)
        ->orWhereLessThan('updated_at', $date)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE verified_at IS NOT NULL AND created_at > ? OR updated_at < ?");
    expect($params)->toBe([$date, $date]);
});

it('generates query using the or operator between the and operators', function () {
    $query = new Query();

    $date = date('Y-m-d');

    $sql = $query->table('users')
        ->whereGreatherThan('created_at', $date)
        ->orWhereLessThan('updated_at', $date)
        ->whereNotNull('verified_at')
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE created_at > ? OR updated_at < ? AND verified_at IS NOT NULL");
    expect($params)->toBe([$date, $date]);
});

it('generates query to select between columns', function (string $method, string $operator) {
    $query = new Query();

    $sql = $query->table('users')
        ->{$method}('age', [20, 30])
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE age {$operator} ? AND ?");
    expect($params)->toBe([20, 30]);
})->with([
    ['whereBetween', Operators::BETWEEN->value],
    ['whereNotBetween', Operators::NOT_BETWEEN->value],
]);
