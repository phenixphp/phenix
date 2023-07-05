<?php

declare(strict_types=1);

namespace Tests\Core\Database\Query;

use Core\Database\Query;

it('generates delete statement', function () {
    $query = new Query();

    $sql = $query->table('users')
        ->whereEqual('id', 1)
        ->delete()
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "DELETE FROM users WHERE id = ?";

    expect($dml)->toBe($expected);
    expect($params)->toBe([1]);
});

it('generates delete statement without clauses', function () {
    $query = new Query();

    $sql = $query->table('users')
        ->delete()
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "DELETE FROM users";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});
