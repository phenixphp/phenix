<?php

declare(strict_types=1);

namespace Tests\Core\Database\Query;

use Core\Database\Query;

use function Pest\Faker\faker;

it('generates insert into statement', function () {
    $query = new Query();

    $name = faker()->name;
    $email = faker()->freeEmail;

    $sql = $query->table('users')
        ->insert([
            'name' => $name,
            'email' => $email,
        ])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "INSERT INTO users (email, name) VALUES ('{$email}', '{$name}')";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});

it('generates insert into statement with data collection', function () {
    $query = new Query();

    $name = faker()->name;
    $email = faker()->freeEmail;

    $sql = $query->table('users')
        ->insert([
            [
                'name' => $name,
                'email' => $email,
            ],
            [
                'name' => $name,
                'email' => $email,
            ],
        ])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "INSERT INTO users (email, name) VALUES ('{$email}', '{$name}'), ('{$email}', '{$name}')";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});
