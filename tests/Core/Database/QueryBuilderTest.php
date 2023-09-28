<?php

declare(strict_types=1);

use Core\Database\Constants\Connections;
use Core\Database\Paginator;
use Core\Database\QueryBuilder;
use Core\Facades\DB;
use Core\Util\URL;
use League\Uri\Uri;
use Tests\Mocks\Database\MysqlConnectionPool;
use Tests\Mocks\Database\Result;
use Tests\Mocks\Database\Statement;

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

it('counts all database records', function () {
    $connection = $this->getMockBuilder(MysqlConnectionPool::class)->getMock();

    $connection->expects($this->exactly(1))
        ->method('prepare')
        ->willReturnOnConsecutiveCalls(
            new Statement(new Result([['COUNT(*)' => 1]])),
        );

    $query = new QueryBuilder();
    $query->setConnection($connection);

    $count = $query->from('users')->count();

    expect($count)->toBe(1);
});

it('paginates the query results', function () {
    $data = [['id' => 1, 'name' => 'John Doe']];

    $connection = $this->getMockBuilder(MysqlConnectionPool::class)->getMock();

    $connection->expects($this->exactly(2))
        ->method('prepare')
        ->willReturnOnConsecutiveCalls(
            new Statement(new Result([['COUNT(*)' => 1]])),
            new Statement(new Result($data))
        );

    $query = new QueryBuilder();
    $query->setConnection($connection);

    $uri = Uri::new(URL::build('users'));

    $paginator = $query->from('users')
        ->select(['id', 'name'])
        ->paginate($uri);

    expect($paginator)->toBeInstanceOf(Paginator::class);
    expect($paginator->toArray())->toBe([
        'path' => URL::build('users'),
        'current_page' => 1,
        'last_page' => 1,
        'per_page' => 15,
        'total' => 1,
        'first_page_url' => URL::build('users', ['page' => 1]),
        'last_page_url' => URL::build('users', ['page' => 1]),
        'prev_page_url' => null,
        'next_page_url' => null,
        'from' => 1,
        'to' => 1,
        'data' => $data,
        'links' => [
            [
                'url' => URL::build('users', ['page' => 1]),
                'label' => 1,
            ],
        ],
    ]);
});
