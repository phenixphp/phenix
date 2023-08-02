<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Data\Collection;
use Core\Database\Concerns\Query\BuildsQuery;
use Core\Database\Concerns\Query\HasJoinClause;
use Core\Database\Constants\Connections;
use Core\Facades\Config;
use stdClass;
use Throwable;

class QueryBuilder extends QueryBase
{
    use BuildsQuery { update as updateRow; }
    use HasJoinClause;

    protected Connections $connection;

    public function __construct()
    {
        parent::__construct();

        $this->connection = Connections::from(Config::get('database.default'));
    }

    public function connection(Connections $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return Collection<int, stdClass>
     */
    public function get(): Collection
    {
        [$dml, $params] = $this->toSql();

        $result = $this->connection->get()
            ->prepare($dml)
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
            $this->connection->get()->prepare($dml)->execute($params);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
