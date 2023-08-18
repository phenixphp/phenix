<?php

declare(strict_types=1);

namespace Core\Database\Constants;

use Amp\Sql\Common\ConnectionPool;
use Core\App;

enum Connections: string
{
    case MYSQL = 'mysql';
    case POSTGRESQL = 'postgresql';

    public static function default(): ConnectionPool
    {
        return App::make('db.connection.default');
    }

    public function get(): ConnectionPool
    {
        return App::make("db.connection.{$this->value}");

    }
}
