<?php

declare(strict_types=1);

namespace Tests\Mocks\Database;

use Amp\Mysql\MysqlConfig;
use Amp\Sql\Common\ConnectionPool;
use Amp\Sql\ConnectionException;
use Amp\Sql\Result;
use Amp\Sql\SqlConfig;
use Amp\Sql\SqlConnector;
use Amp\Sql\Statement;
use Amp\Sql\Transaction;
use Mockery;
use Tests\Mocks\Database\Result as FakeResult;
use Tests\Mocks\Database\Statement as FakeStatement;
use Throwable;

class MysqlConnectionPool extends ConnectionPool
{
    protected FakeResult $fakeResult;
    protected Throwable|null $fakeError = null;

    public function __construct(SqlConfig|null $config = null, SqlConnector|null $connector = null)
    {
        parent::__construct(
            $config ?? MysqlConfig::fromString('host=host;user=user;password=password'),
            $connector ?? Mockery::mock(SqlConnector::class)
        );
    }

    public static function fake(array $result = []): self
    {
        $pool = new self();
        $pool->setFakeResult($result);

        return $pool;
    }

    public function setFakeResult(array $result): void
    {
        $this->fakeResult = new FakeResult($result);
    }

    public function throwDatabaseException(Throwable|null $error = null): self
    {
        $this->fakeError = $error ?? new ConnectionException('Fail trying database connection');

        return $this;
    }

    public function prepare(string $sql): Statement
    {
        if (isset($this->fakeError)) {
            throw $this->fakeError;
        }

        return new FakeStatement($this->fakeResult);
    }

    protected function createStatement(Statement $statement, \Closure $release): Statement
    {
        return $statement;
    }

    protected function createResult(Result $result, \Closure $release): Result
    {
        return $result;
    }

    protected function createStatementPool(string $sql, \Closure $prepare): Statement
    {
        return new FakeStatement($this->fakeResult);

    }

    protected function createTransaction(Transaction $transaction, \Closure $release): Transaction
    {
        return $transaction;
    }
}
