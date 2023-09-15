<?php

declare(strict_types=1);

use Core\Database\QueryGenerator;

use function Pest\Faker\faker;

it('generates update statement', function () {
    $query = new QueryGenerator();

    $name = faker()->name;

    $sql = $query->table('users')
        ->whereEqual('id', 1)
        ->update(['name' => $name])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "UPDATE users SET name = ? WHERE id = ?";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$name, 1]);
});

it('generates update statement with many conditions and columns', function () {
    $query = new QueryGenerator();

    $name = faker()->name;

    $sql = $query->table('users')
        ->whereNotNull('verified_at')
        ->whereEqual('role_id', 2)
        ->update(['name' => $name, 'active' => true])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "UPDATE users SET name = ?, active = ? WHERE verified_at IS NOT NULL AND role_id = ?";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$name, true, 2]);
});
