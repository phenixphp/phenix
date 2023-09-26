<?php

declare(strict_types=1);

use Core\Database\QueryGenerator;

it('generates offset pagination query', function () {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->page()
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users LIMIT 15 OFFSET 0');
    expect($params)->toBeEmpty($params);
});

it('generates offset pagination query with indicate page', function () {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->page(3)
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users LIMIT 15 OFFSET 45');
    expect($params)->toBeEmpty($params);
});

it('overwrites limit when pagination is called', function () {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->selectAllColumns()
        ->limit(5)
        ->page(2)
        ->toSql();

    [$dml, $params] = $sql;

    expect($dml)->toBe('SELECT * FROM users LIMIT 15 OFFSET 30');
    expect($params)->toBeEmpty($params);
});
