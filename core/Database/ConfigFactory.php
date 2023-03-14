<?php

declare(strict_types=1);

namespace Core\Database;

use Amp\Mysql\MysqlConfig;
use Amp\Sql\SqlConfig;
use Core\Contracts\Makeable;
use Core\Exceptions\RuntimeError;
use Core\Facades\Config;

class ConfigFactory implements Makeable
{
    public static function make(string $connection): SqlConfig
    {
        $connection = Config::get("database.connections.{$connection}");

        return match ($connection['driver']) {
            'mysql' => new MysqlConfig(
                host: $connection['host'],
                port: (int) $connection['port'] ?: MysqlConfig::DEFAULT_PORT,
                user: $connection['username'],
                password: $connection['password'],
                database: $connection['database'],
                charset: $connection['charset'] ?: MysqlConfig::DEFAULT_CHARSET,
                collate: $connection['collation'] ?: MysqlConfig::DEFAULT_COLLATE
            ),
            default => throw new RuntimeError("Unsupported driver {$connection['driver']}")
        };
    }
}
