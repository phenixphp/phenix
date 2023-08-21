<?php

declare(strict_types=1);

namespace Tests\Core\Database;

use Core\Database\Constants\Connections;
use Core\Database\QueryBuilder;
use Core\Facades\DB;
use Tests\Mocks\Database\MysqlConnectionPool;

it('gets all records from database', function () {
    $data = [
        ['id' => 1, 'name' => 'John Doe'],
    ];

    $this->app->swap(Connections::default(), MysqlConnectionPool::fake($data));

    $query = new QueryBuilder();

    $result = $query->from('users')
        ->select(['id', 'name'])
        ->get();

    expect($result->toArray())->toBe($data);
});

it('gets all records from database using facade', function () {
    $data = [
        ['id' => 1, 'name' => 'John Doe'],
    ];

    $this->app->swap(Connections::default(), MysqlConnectionPool::fake($data));

    $result = DB::from('users')
        ->select(['id', 'name'])
        ->get();

    expect($result->toArray())->toBe($data);
});

it('gets the first record from database', function () {
    $data = [
        ['id' => 1, 'name' => 'John Doe'],
    ];

    $this->app->swap(Connections::default(), MysqlConnectionPool::fake($data));

    $query = new QueryBuilder();

    $result = $query->from('users')
        ->select(['id', 'name'])
        ->first();

    expect($result)->toBe($data[0]);
});

it('sets custom connection', function () {
    $data = [
        ['id' => 1, 'name' => 'John Doe'],
    ];

    $this->app->swap(Connections::name('mysql'), MysqlConnectionPool::fake($data));

    $result = DB::connection('mysql')
        ->from('users')
        ->select(['id', 'name'])
        ->get();

    expect($result->toArray())->toBe($data);
});

it('updates records', function () {
    $data = [
        ['id' => 1, 'name' => 'John Doe'],
    ];

    $this->app->swap(Connections::default(), MysqlConnectionPool::fake($data));

    $result = DB::from('users')
        ->whereEqual('id', 1)
        ->update(['name' => 'Tony']);

    expect($result)->toBeTrue();
});

it('fails on record update', function () {
    $data = [
        ['id' => 1, 'name' => 'John Doe'],
    ];

    $this->app->swap(Connections::default(), MysqlConnectionPool::fake($data)->throwDatabaseException());

    $result = DB::from('users')
        ->whereEqual('id', 1)
        ->update(['name' => 'Tony']);

    expect($result)->toBeFalse();
});
