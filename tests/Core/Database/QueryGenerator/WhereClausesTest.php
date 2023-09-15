<?php

declare(strict_types=1);

use Core\Database\Constants\Operators;
use Core\Database\Constants\Order;
use Core\Database\Functions;
use Core\Database\QueryGenerator;
use Core\Database\Subquery;

it('generates query to select a record by column', function () {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->whereEqual('id', 1)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users WHERE id = ?');
    expect($params)->toBe([1]);
});

it('generates query to select a record using many clause', function () {
    $query = new QueryGenerator();

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

it('generates query to select using comparison clause', function (
    string $method,
    string $column,
    string $operator,
    string|int $value
) {
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->whereEqual('id', 1)
        ->select(['id', 'name', 'email'])
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT id, name, email FROM users WHERE id = ?');
    expect($params)->toBe([1]);
});


it('generates query using in and not in operators', function (string $method, string $operator) {
    $query = new QueryGenerator();

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

it('generates query using in and not in operators with subquery', function (string $method, string $operator) {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->{$method}('id', function (Subquery $query) {
            $query->select(['id'])
                ->from('users')
                ->whereGreatherThanOrEqual('created_at', date('Y-m-d'));
        })
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    $date = date('Y-m-d');

    $expected = "SELECT * FROM users WHERE id {$operator} "
        . "(SELECT id FROM users WHERE created_at >= ?)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$date]);
})->with([
    ['whereIn', Operators::IN->value],
    ['whereNotIn', Operators::NOT_IN->value],
]);

it('generates query to select null or not null columns', function (string $method, string $operator) {
    $query = new QueryGenerator();

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

it('generates query to select by column or null or not null columns', function (string $method, string $operator) {
    $query = new QueryGenerator();

    $date = date('Y-m-d');

    $sql = $query->table('users')
        ->whereGreatherThan('created_at', $date)
        ->{$method}('verified_at')
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE created_at > ? OR verified_at {$operator}");
    expect($params)->toBe([$date]);
})->with([
    ['orWhereNull', Operators::IS_NULL->value],
    ['orWhereNotNull', Operators::IS_NOT_NULL->value],
]);

it('generates query to select boolean columns', function (string $method, string $operator) {
    $query = new QueryGenerator();

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

it('generates query to select by column or boolean column', function (string $method, string $operator) {
    $query = new QueryGenerator();

    $date = date('Y-m-d');

    $sql = $query->table('users')
        ->whereGreatherThan('created_at', $date)
        ->{$method}('enabled')
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE created_at > ? OR enabled {$operator}");
    expect($params)->toBe([$date]);
})->with([
    ['orWhereTrue', Operators::IS_TRUE->value],
    ['orWhereFalse', Operators::IS_FALSE->value],
]);

it('generates query using logical connectors', function () {
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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

it('generates queries using logical connectors', function (
    string $method,
    string $column,
    array|string $value,
    string $operator
) {
    $placeholders = '?';

    if (\is_array($value)) {
        $params = array_pad([], count($value), '?');

        $placeholders = '(' . implode(', ', $params) . ')';
    }

    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->whereNotNull('verified_at')
        ->{$method}($column, $value)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE verified_at IS NOT NULL OR {$column} {$operator} {$placeholders}");
    expect($params)->toBe([...(array)$value]);
})->with([
    ['orWhereLessThan', 'updated_at', date('Y-m-d'), Operators::LESS_THAN->value],
    ['orWhereEqual', 'updated_at', date('Y-m-d'), Operators::EQUAL->value],
    ['orWhereDistinct', 'updated_at', date('Y-m-d'), Operators::DISTINCT->value],
    ['orWhereGreatherThan', 'updated_at', date('Y-m-d'), Operators::GREATHER_THAN->value],
    ['orWhereGreatherThanOrEqual', 'updated_at', date('Y-m-d'), Operators::GREATHER_THAN_OR_EQUAL->value],
    ['orWhereLessThan', 'updated_at', date('Y-m-d'), Operators::LESS_THAN->value],
    ['orWhereLessThanOrEqual', 'updated_at', date('Y-m-d'), Operators::LESS_THAN_OR_EQUAL->value],
    ['orWhereIn', 'status', ['enabled', 'verified'], Operators::IN->value],
    ['orWhereNotIn', 'status', ['disabled', 'banned'], Operators::NOT_IN->value],
]);

it('generates query to select between columns', function (string $method, string $operator) {
    $query = new QueryGenerator();

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

it('generates query to select by column or between columns', function (string $method, string $operator) {
    $query = new QueryGenerator();

    $date = date('Y-m-d');
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d');

    $sql = $query->table('users')
        ->whereGreatherThan('created_at', $date)
        ->{$method}('updated_at', [$startDate, $endDate])
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE created_at > ? OR updated_at {$operator} ? AND ?");
    expect($params)->toBe([$date, $startDate, $endDate]);
})->with([
    ['orWhereBetween', Operators::BETWEEN->value],
    ['orWhereNotBetween', Operators::NOT_BETWEEN->value],
]);

it('generates a column-ordered query', function (array|string $column, string $order) {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->orderBy($column, Order::from($order))
        ->toSql();

    [$dml, $params] = $sql;

    $operator = Operators::ORDER_BY->value;

    $column = implode(', ', (array) $column);

    expect($dml)->toBe("SELECT * FROM users {$operator} {$column} {$order}");
    expect($params)->toBe($params);
})->with([
    ['id', Order::ASC->value],
    [['id', 'created_at'], Order::ASC->value],
    ['id', Order::DESC->value],
    [['id', 'created_at'], Order::DESC->value],
]);

it('generates a column-ordered query using select-case', function () {
    $case = Functions::case()
        ->whenNull('city', 'country')
        ->defaultResult('city');

    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->orderBy($case, Order::ASC)
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users ORDER BY (CASE WHEN city IS NULL THEN country ELSE city END) ASC");
    expect($params)->toBe($params);
});

it('generates a limited query', function (array|string $column, string $order) {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->whereEqual('id', 1)
        ->selectAllColumns()
        ->orderBy($column, Order::from($order))
        ->limit(1)
        ->toSql();

    [$dml, $params] = $sql;

    $operator = Operators::ORDER_BY->value;

    $column = implode(', ', (array) $column);

    expect($dml)->toBe("SELECT * FROM users WHERE id = ? {$operator} {$column} {$order} LIMIT 1");
    expect($params)->toBe([1]);
})->with([
    ['id', Order::ASC->value],
    [['id', 'created_at'], Order::ASC->value],
    ['id', Order::DESC->value],
    [['id', 'created_at'], Order::DESC->value],
]);

it('generates a query with a exists subquery in where clause', function (string $method, string $operator) {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->{$method}(function (Subquery $query) {
            $query->table('user_role')
                ->selectAllColumns()
                ->whereEqual('user_id', 1)
                ->whereEqual('role_id', 9)
                ->limit(1);
        })
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT * FROM users WHERE {$operator} "
        . "(SELECT * FROM user_role WHERE user_id = ? AND role_id = ? LIMIT 1)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([1, 9]);
})->with([
    ['whereExists', Operators::EXISTS->value],
    ['whereNotExists', Operators::NOT_EXISTS->value],
]);

it('generates a query to select by column or when exists or not exists subquery', function (
    string $method,
    string $operator
) {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->whereTrue('is_admin')
        ->{$method}(function (Subquery $query) {
            $query->table('user_role')
                ->selectAllColumns()
                ->whereEqual('user_id', 1)
                ->limit(1);
        })
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT * FROM users WHERE is_admin IS TRUE OR {$operator} "
        . "(SELECT * FROM user_role WHERE user_id = ? LIMIT 1)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([1]);
})->with([
    ['orWhereExists', Operators::EXISTS->value],
    ['orWhereNotExists', Operators::NOT_EXISTS->value],
]);

it('generates query to select using comparison clause with subqueries and functions', function (
    string $method,
    string $column,
    string $operator
) {
    $query = new QueryGenerator();

    $sql = $query->table('products')
        ->{$method}($column, function (Subquery $subquery) {
            $subquery->select([Functions::max('price')])->from('products');
        })
        ->selectAllColumns()
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT * FROM products WHERE {$column} {$operator} "
        . '(SELECT ' . Functions::max('price') . ' FROM products)';

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
})->with([
    ['whereEqual', 'price', Operators::EQUAL->value],
    ['whereDistinct', 'price', Operators::DISTINCT->value],
    ['whereGreatherThan', 'price', Operators::GREATHER_THAN->value],
    ['whereGreatherThanOrEqual', 'price', Operators::GREATHER_THAN_OR_EQUAL->value],
    ['whereLessThan', 'price', Operators::LESS_THAN->value],
    ['whereLessThanOrEqual', 'price', Operators::LESS_THAN_OR_EQUAL->value],
]);

it('generates query using comparison clause with subqueries and any, all, some operators', function (
    string $method,
    string $comparisonOperator,
    string $operator
) {
    $query = new QueryGenerator();

    $sql = $query->table('products')
        ->{$method}('id', function (Subquery $subquery) {
            $subquery->select(['product_id'])
                ->from('orders')
                ->whereGreatherThan('quantity', 10);
        })
        ->select(['description'])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT description FROM products WHERE id {$comparisonOperator} {$operator}"
        . "(SELECT product_id FROM orders WHERE quantity > ?)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([10]);
})->with([
    ['whereAnyEqual', Operators::EQUAL->value, Operators::ANY->value],
    ['whereAnyDistinct', Operators::DISTINCT->value, Operators::ANY->value],
    ['whereAnyGreatherThan', Operators::GREATHER_THAN->value, Operators::ANY->value],
    ['whereAnyGreatherThanOrEqual', Operators::GREATHER_THAN_OR_EQUAL->value, Operators::ANY->value],
    ['whereAnyLessThan', Operators::LESS_THAN->value, Operators::ANY->value],
    ['whereAnyLessThanOrEqual', Operators::LESS_THAN_OR_EQUAL->value, Operators::ANY->value],

    ['whereAllEqual', Operators::EQUAL->value, Operators::ALL->value],
    ['whereAllDistinct', Operators::DISTINCT->value, Operators::ALL->value],
    ['whereAllGreatherThan', Operators::GREATHER_THAN->value, Operators::ALL->value],
    ['whereAllGreatherThanOrEqual', Operators::GREATHER_THAN_OR_EQUAL->value, Operators::ALL->value],
    ['whereAllLessThan', Operators::LESS_THAN->value, Operators::ALL->value],
    ['whereAllLessThanOrEqual', Operators::LESS_THAN_OR_EQUAL->value, Operators::ALL->value],

    ['whereSomeEqual', Operators::EQUAL->value, Operators::SOME->value],
    ['whereSomeDistinct', Operators::DISTINCT->value, Operators::SOME->value],
    ['whereSomeGreatherThan', Operators::GREATHER_THAN->value, Operators::SOME->value],
    ['whereSomeGreatherThanOrEqual', Operators::GREATHER_THAN_OR_EQUAL->value, Operators::SOME->value],
    ['whereSomeLessThan', Operators::LESS_THAN->value, Operators::SOME->value],
    ['whereSomeLessThanOrEqual', Operators::LESS_THAN_OR_EQUAL->value, Operators::SOME->value],
]);

it('generates query with row subquery', function (string $method, string $operator) {
    $query = new QueryGenerator();

    $sql = $query->table('employees')
        ->{$method}(['manager_id', 'department_id'], function (Subquery $subquery) {
            $subquery->select(['id, department_id'])
                ->from('managers')
                ->whereEqual('location_id', 1);
        })
        ->select(['name'])
        ->toSql();

    [$dml, $params] = $sql;

    $subquery = 'SELECT id, department_id FROM managers WHERE location_id = ?';

    $expected = "SELECT name FROM employees "
        . "WHERE ROW(manager_id, department_id) {$operator} ({$subquery})";

    expect($dml)->toBe($expected);
    expect($params)->toBe([1]);
})->with([
    ['whereRowEqual', Operators::EQUAL->value],
    ['whereRowDistinct', Operators::DISTINCT->value],
    ['whereRowGreatherThan', Operators::GREATHER_THAN->value],
    ['whereRowGreatherThanOrEqual', Operators::GREATHER_THAN_OR_EQUAL->value],
    ['whereRowLessThan', Operators::LESS_THAN->value],
    ['whereRowLessThanOrEqual', Operators::LESS_THAN_OR_EQUAL->value],
    ['whereRowIn', Operators::IN->value],
    ['whereRowNotIn', Operators::NOT_IN->value],
]);
