<?php

declare(strict_types=1);

namespace Core\Database;

use Amp\Sql\Common\ConnectionPool;
use Core\App;
use Core\Data\Collection;
use Core\Database\Concerns\Query\BuildsQuery;
use Core\Database\Concerns\Query\HasJoinClause;
use Core\Database\Constants\Connections;
use Throwable;

class QueryBuilder extends QueryBase
{
    use BuildsQuery { update as updateRow; }
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
        ;

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

    public function update(array $values)
    {
        $this->updateRow($values);

        [$dml, $params] = $this->toSql();

        try {
            $this->connection->prepare($dml)->execute($params);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
