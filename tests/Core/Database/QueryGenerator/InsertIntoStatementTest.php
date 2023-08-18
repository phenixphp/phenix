<?php

declare(strict_types=1);

namespace Tests\Core\Database\QueryGenerator;

use Core\Database\QueryGenerator;
use Core\Database\Subquery;

use function Pest\Faker\faker;

it('generates insert into statement', function () {
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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
    $query = new QueryGenerator();

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


it('generates insert statement from subquery', function () {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->insertFrom(function (Subquery $subquery) {
            $subquery->table('customers')
                ->select(['name', 'email'])
                ->whereNotNull('verified_at');
        }, ['name', 'email'])
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "INSERT INTO users (name, email) SELECT name, email FROM customers WHERE verified_at IS NOT NULL";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});

it('generates insert ignore statement from subquery', function () {
    $query = new QueryGenerator();

    $sql = $query->table('users')
        ->insertFrom(function (Subquery $subquery) {
            $subquery->table('customers')
                ->select(['name', 'email'])
                ->whereNotNull('verified_at');
        }, ['name', 'email'], true)
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "INSERT IGNORE INTO users (name, email) "
        . "SELECT name, email FROM customers WHERE verified_at IS NOT NULL";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
});
