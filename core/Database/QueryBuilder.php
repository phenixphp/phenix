<?php

declare(strict_types=1);

namespace Core\Database;

use Amp\Sql\Common\ConnectionPool;
use Amp\Sql\QueryError;
use Amp\Sql\TransactionError;
use Core\App;
use Core\Data\Collection;
use Core\Database\Concerns\Query\BuildsQuery;
use Core\Database\Concerns\Query\HasJoinClause;
use Core\Database\Constants\Connections;
use League\Uri\Components\Query;
use League\Uri\Uri;

class QueryBuilder extends QueryBase
{
    use BuildsQuery {
        insert as protected insertRows;
        update as protected updateRow;
        count as protected countRows;
        exists as protected existsRows;
        doesntExist as protected doesntExistRows;
    }
    use HasJoinClause;

    protected ConnectionPool $connection;

    public function __construct()
    {
        parent::__construct();

        $this->connection = App::make(Connections::default());
    }

    public function connection(string $connection): self
    {
        $this->connection = App::make(Connections::name($connection));

        return $this;
    }

    public function setConnection(ConnectionPool $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return Collection<int, array>
     */
    public function get(): Collection
    {
        [$dml, $params] = $this->toSql();

        $result = $this->connection->prepare($dml)
            ->execute($params);

        $collection = new Collection('array');

        foreach ($result as $row) {
            $collection->add($row);
        }

        return $collection;
    }

    /**
     * @return array<string, mixed>
     */
    public function first(): array
    {
        return $this->get()->first();
    }

    public function paginate(Uri $uri,  int $defaultPage = 1, int $defaultPerPage = 15): Paginator
    {
        $query = Query::fromUri($uri);

        $currentPage = filter_var($query->get('page') ?? $defaultPage, FILTER_SANITIZE_NUMBER_INT);
        $currentPage = $currentPage === false ? $defaultPage : $currentPage;

        $perPage = filter_var($query->get('per_page') ?? $defaultPerPage, FILTER_SANITIZE_NUMBER_INT);
        $perPage = $perPage === false ? $defaultPerPage : $perPage;

        $total = (new self())->setConnection($this->connection)
            ->from($this->table)
            ->count();

        $data = $this->page((int) $currentPage, (int) $perPage)->get();

        return new Paginator($uri, $data, (int) $total, (int) $currentPage, (int) $perPage);
    }

    public function count(string $column = '*'): int
    {
        $this->countRows($column);

        [$dml, $params] = $this->toSql();

        /** @var array<string, int> $count */
        $count = $this->connection
            ->prepare($dml)
            ->execute($params)
            ->fetchRow();

        return array_values($count)[0];
    }

    public function insert(array $data): bool
    {
        $this->insertRows($data);

        [$dml, $params] = $this->toSql();

        try {
            $this->connection->prepare($dml)->execute($params)->fetchRow();

            return true;
        } catch (QueryError|TransactionError) {
            return false;
        }
    }

    public function exists(): bool
    {
        $this->existsRows();

        [$dml, $params] = $this->toSql();

        $results = $this->connection->prepare($dml)->execute($params)->fetchRow();

        return (bool) array_values($results)[0];
    }

    public function doesntExist(): bool
    {
        return ! $this->exists();
    }

    public function update(array $values): bool
    {
        $this->updateRow($values);

        [$dml, $params] = $this->toSql();

        try {
            $this->connection->prepare($dml)->execute($params);

            return true;
        } catch (QueryError|TransactionError) {
            return false;
        }
    }
}
