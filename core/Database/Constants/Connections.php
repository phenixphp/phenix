<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum Connections: string
{
    case PREFIX = 'database.connections.';

    public static function default(): string
    {
        return self::name('default');
    }

    public static function name(string $connection): string
    {
        return self::PREFIX->value . $connection;
    }
}
