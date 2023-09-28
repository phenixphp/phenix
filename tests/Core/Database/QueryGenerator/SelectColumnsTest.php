<?php

declare(strict_types=1);

use Core\Database\Alias;
use Core\Database\Constants\Operators;
use Core\Database\Functions;
use Core\Database\QueryGenerator;
use Core\Database\Subquery;
use Core\Database\Value;
use Core\Exceptions\QueryError;

it('generates query to select all columns of table', function () {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users');
    expect($params)->toBeEmpty($params);
});

it('generates query to select all columns from table', function () {
    $query = new QueryGenerator();

    $sql = $query->selectAllColumns()
        ->from('users')
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users');
    expect($params)->toBeEmpty($params);
});

it('generates a query using sql functions', function (string $function, string $column, string $rawFunction) {
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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
        $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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

it('generates query with select-cases using comparisons', function (
    string $method,
    array $data,
    string $defaultResult,
    string $operator
) {
    [$column, $value, $result] = $data;

    $value = Value::from($value);

    $query = new QueryGenerator();

    $case = Functions::case()
        ->{$method}($column, $value, $result)
        ->defaultResult($defaultResult)
        ->as('type');

    $sql = $query->select([
            'id',
            'description',
            $case,
        ])
        ->from('products')
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT id, description, (CASE WHEN {$column} {$operator} {$value} "
        . "THEN {$result} ELSE $defaultResult END) AS type FROM products";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
})->with([
    ['whenEqual', ['price', 100, 'expensive'], 'cheap', Operators::EQUAL->value],
    ['whenDistinct', ['price', 100, 'expensive'], 'cheap', Operators::DISTINCT->value],
    ['whenGreatherThan', ['price', 100, 'expensive'], 'cheap', Operators::GREATHER_THAN->value],
    ['whenGreatherThanOrEqual', ['price', 100, 'expensive'], 'cheap', Operators::GREATHER_THAN_OR_EQUAL->value],
    ['whenLessThan', ['price', 100, 'cheap'], 'expensive', Operators::LESS_THAN->value],
    ['whenLessThanOrEqual', ['price', 100, 'cheap'], 'expensive', Operators::LESS_THAN_OR_EQUAL->value],
]);

it('generates query with select-cases using logical comparisons', function (
    string $method,
    array $data,
    string $defaultResult,
    string $operator
) {
    [$column, $result] = $data;

    $query = new QueryGenerator();

    $case = Functions::case()
        ->{$method}(...$data)
        ->defaultResult($defaultResult)
        ->as('status');

    $sql = $query->select([
            'id',
            'name',
            $case,
        ])
        ->from('users')
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT id, name, (CASE WHEN {$column} {$operator} "
        . "THEN {$result} ELSE $defaultResult END) AS status FROM users";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
})->with([
    ['whenNull', ['created_at', 'inactive'], 'active', Operators::IS_NULL->value],
    ['whenNotNull', ['created_at', 'active'], 'inactive', Operators::IS_NOT_NULL->value],
    ['whenTrue', ['is_verified', 'active'], 'inactive', Operators::IS_TRUE->value],
    ['whenFalse', ['is_verified', 'inactive'], 'active', Operators::IS_FALSE->value],
]);

it('generates query with select-cases with multiple conditions and string values', function () {
    $date = date('Y-m-d H:i:s');

    $query = new QueryGenerator();

    $case = Functions::case()
        ->whenNull('created_at', Value::from('inactive'))
        ->whenGreatherThan('created_at', Value::from($date), Value::from('new user'))
        ->defaultResult(Value::from('old user'))
        ->as('status');

    $sql = $query->select([
            'id',
            'name',
            $case,
        ])
        ->from('users')
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT id, name, (CASE WHEN created_at IS NULL THEN 'inactive' "
        . "WHEN created_at > '{$date}' THEN 'new user' ELSE 'old user' END) AS status FROM users";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});

it('generates query with select-cases without default value', function () {
    $date = date('Y-m-d H:i:s');

    $query = new QueryGenerator();

    $case = Functions::case()
        ->whenNull('created_at', Value::from('inactive'))
        ->whenGreatherThan('created_at', Value::from($date), Value::from('new user'))
        ->as('status');

    $sql = $query->select([
            'id',
            'name',
            $case,
        ])
        ->from('users')
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT id, name, (CASE WHEN created_at IS NULL THEN 'inactive' "
        . "WHEN created_at > '{$date}' THEN 'new user' END) AS status FROM users";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});

it('generates query with select-case using functions', function () {
    $query = new QueryGenerator();

    $case = Functions::case()
        ->whenGreatherThanOrEqual(Functions::avg('price'), 4, Value::from('expensive'))
        ->defaultResult(Value::from('cheap'))
        ->as('message');

    $sql = $query->select([
            'id',
            'description',
            'price',
            $case,
        ])
        ->from('products')
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT id, description, price, (CASE WHEN AVG(price) >= 4 THEN 'expensive' ELSE 'cheap' END) "
        . "AS message FROM products";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});

it('counts all records', function () {
    $query = new QueryGenerator();

    $sql = $query->from('products')
        ->count()
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT COUNT(*) FROM products";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});
