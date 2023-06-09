<?php

declare(strict_types=1);

namespace Tests\Core\Database\Query;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Core\Database\Constants\Operators;
use Core\Database\Query;

it('generates query to select a record by date', function (
    string $method,
    CarbonInterface|string $date,
    string $value,
    string $operator
) {
    $query = new Query();

    $sql = $query->table('users')
        ->{$method}('created_at', $date)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE DATE(created_at) {$operator} ?");
    expect($params)->toBe([$value]);
})->with([
    ['whereDateEqual', Carbon::now(), Carbon::now()->format('Y-m-d'), Operators::EQUAL->value],
    ['whereDateEqual', date('Y-m-d'), date('Y-m-d'), Operators::EQUAL->value],
    ['whereDateGreatherThan', date('Y-m-d'), date('Y-m-d'), Operators::GREATHER_THAN->value],
    ['whereDateGreatherThanOrEqual', date('Y-m-d'), date('Y-m-d'), Operators::GREATHER_THAN_OR_EQUAL->value],
    ['whereDateLessThan', date('Y-m-d'), date('Y-m-d'), Operators::LESS_THAN->value],
    ['whereDateLessThanOrEqual', date('Y-m-d'), date('Y-m-d'), Operators::LESS_THAN_OR_EQUAL->value],
]);

it('generates query to select a record by condition or by date', function (
    string $method,
    CarbonInterface|string $date,
    string $value,
    string $operator
) {
    $query = new Query();

    $sql = $query->table('users')
        ->whereFalse('active')
        ->{$method}('created_at', $date)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE active IS FALSE OR DATE(created_at) {$operator} ?");
    expect($params)->toBe([$value]);
})->with([
    ['orWhereDateEqual', date('Y-m-d'), date('Y-m-d'), Operators::EQUAL->value],
    ['orWhereDateGreatherThan', date('Y-m-d'), date('Y-m-d'), Operators::GREATHER_THAN->value],
    ['orWhereDateGreatherThanOrEqual', date('Y-m-d'), date('Y-m-d'), Operators::GREATHER_THAN_OR_EQUAL->value],
    ['orWhereDateLessThan', date('Y-m-d'), date('Y-m-d'), Operators::LESS_THAN->value],
    ['orWhereDateLessThanOrEqual', date('Y-m-d'), date('Y-m-d'), Operators::LESS_THAN_OR_EQUAL->value],
]);

it('generates query to select a record by month', function (
    string $method,
    CarbonInterface|int $date,
    int $value,
    string $operator
) {
    $query = new Query();

    $sql = $query->table('users')
        ->{$method}('created_at', $date)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE MONTH(created_at) {$operator} ?");
    expect($params)->toBe([$value]);
})->with([
    ['whereMonthEqual', Carbon::now(), Carbon::now()->format('m'), Operators::EQUAL->value],
    ['whereMonthEqual', date('m'), date('m'), Operators::EQUAL->value],
    ['whereMonthGreatherThan', date('m'), date('m'), Operators::GREATHER_THAN->value],
    ['whereMonthGreatherThanOrEqual', date('m'), date('m'), Operators::GREATHER_THAN_OR_EQUAL->value],
    ['whereMonthLessThan', date('m'), date('m'), Operators::LESS_THAN->value],
    ['whereMonthLessThanOrEqual', date('m'), date('m'), Operators::LESS_THAN_OR_EQUAL->value],
]);

it('generates query to select a record by condition or by month', function (
    string $method,
    CarbonInterface|int $date,
    int $value,
    string $operator
) {
    $query = new Query();

    $sql = $query->table('users')
        ->whereFalse('active')
        ->{$method}('created_at', $date)
        ->selectAllColumns()
        ->toSql();

    expect($sql)->toBeArray();

    [$dml, $params] = $sql;

    expect($dml)->toBe("SELECT * FROM users WHERE active IS FALSE OR MONTH(created_at) {$operator} ?");
    expect($params)->toBe([$value]);
})->with([
    ['orWhereMonthEqual', Carbon::now(), Carbon::now()->format('m'), Operators::EQUAL->value],
    ['orWhereMonthEqual', date('m'), date('m'), Operators::EQUAL->value],
    ['orWhereMonthGreatherThan', date('m'), date('m'), Operators::GREATHER_THAN->value],
    ['orWhereMonthGreatherThanOrEqual', date('m'), date('m'), Operators::GREATHER_THAN_OR_EQUAL->value],
    ['orWhereMonthLessThan', date('m'), date('m'), Operators::LESS_THAN->value],
    ['orWhereMonthLessThanOrEqual', date('m'), date('m'), Operators::LESS_THAN_OR_EQUAL->value],
]);