<?php

declare(strict_types=1);

namespace Tests\Core\Database\Query;

use Core\Database\Alias;
use Core\Database\Functions;
use Core\Database\Query;
use Core\Database\Subquery;
use Core\Exceptions\QueryError;

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

it('generates a query using sql functions', function (string $function, string $column, string $rawFunction) {
    $query = new Query();

    $sql = $query->table('products')
        ->select([Functions::{$function}($column)])
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT {$rawFunction} FROM products");
    expect($params)->toBeEmpty();
})->with([
    ['avg', 'price', 'AVG(price)'],
    ['sum', 'price', 'SUM(price)'],
    ['min', 'price', 'MIN(price)'],
    ['max', 'price', 'MAX(price)'],
    ['count', 'id', 'COUNT(id)'],
]);

it('generates a query using sql functions with alias', function (
    string $function,
    string $column,
    string $alias,
    string $rawFunction
) {
    $query = new Query();

    $sql = $query->table('products')
        ->select([Functions::{$function}($column)->as($alias)])
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT {$rawFunction} FROM products");
    expect($params)->toBeEmpty();
})->with([
    ['avg', 'price', 'value', 'AVG(price) AS value'],
    ['sum', 'price', 'value', 'SUM(price) AS value'],
    ['min', 'price', 'value', 'MIN(price) AS value'],
    ['max', 'price', 'value', 'MAX(price) AS value'],
    ['count', 'id', 'value', 'COUNT(id) AS value'],
]);

it('selects field from subquery', function () {
    $query = new Query();

    $date = date('Y-m-d');
    $sql = $query->select(['id', 'name', 'email'])
        ->from(function (Subquery $subquery) use ($date) {
            $subquery->selectAllColumns()
                ->from('users')
                ->whereEqual('verified_at', $date);
        })
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT id, name, email FROM (SELECT * FROM users WHERE verified_at = ?)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$date]);
});


it('generates query using subqueries in column selection', function () {
    $query = new Query();

    $sql = $query->select([
            'id',
            'name',
            Subquery::make()->select(['name'])
                ->from('countries')
                ->whereColumn('users.country_id', 'countries.id')
                ->as('country_name')
                ->limit(1),
        ])
        ->from('users')
        ->toSql();

    [$dml, $params] = $sql;

    $subquery = "SELECT name FROM countries WHERE users.country_id = countries.id LIMIT 1";
    $expected = "SELECT id, name, ({$subquery}) AS country_name FROM users";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});

it('throws exception on generate query using subqueries in column selection with limit missing', function () {
    expect(function () {
        $query = new Query();

        $query->select([
                'id',
                'name',
                Subquery::make()->select(['name'])
                    ->from('countries')
                    ->whereColumn('users.country_id', 'countries.id')
                    ->as('country_name'),
            ])
            ->from('users')
            ->toSql();
    })->toThrow(QueryError::class);
});

it('generates query with column alias', function () {
    $query = new Query();

    $sql = $query->select([
            'id',
            Alias::of('name')->as('full_name'),
        ])
        ->from('users')
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT id, name AS full_name FROM users";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});
