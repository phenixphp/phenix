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

    $expected = "INSERT INTO users (email, name) VALUES (?, ?)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$email, $name]);
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

    $expected = "INSERT INTO users (email, name) VALUES (?, ?), (?, ?)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$email, $name, $email, $name]);
});

it('generates insert ignore into statement', function () {
    $query = new Query();

    $name = faker()->name;
    $email = faker()->freeEmail;

    $sql = $query->table('users')
        ->insertOrIgnore([
            'name' => $name,
            'email' => $email,
        ])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "INSERT IGNORE INTO users (email, name) VALUES (?, ?)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$email, $name]);
});

it('generates upsert statement to handle duplicate keys', function () {
    $query = new Query();

    $name = faker()->name;
    $email = faker()->freeEmail;

    $sql = $query->table('users')
        ->upsert([
            'name' => $name,
            'email' => $email,
        ], ['name'])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "INSERT INTO users (email, name) VALUES (?, ?) "
        . "ON DUPLICATE KEY UPDATE name = VALUES(name)";

    expect($dml)->toBe($expected);
    expect($params)->toBe([$email, $name]);
});

it('generates upsert statement to handle duplicate keys with many unique columns', function () {
    $query = new Query();

    $data = [
        'name' => faker()->name,
        'username' => faker()->userName,
        'email' => faker()->freeEmail,
    ];

    $sql = $query->table('users')
        ->upsert($data, ['name', 'username'])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "INSERT INTO users (email, name, username) VALUES (?, ?, ?) "
        . "ON DUPLICATE KEY UPDATE name = VALUES(name), username = VALUES(username)";

    \ksort($data);

    expect($dml)->toBe($expected);
    expect($params)->toBe(\array_values($data));
});
