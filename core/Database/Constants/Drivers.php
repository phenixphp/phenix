<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum Drivers: string
{
    case MYSQL = 'mysql';
    case POSTGRESQL = 'postgresql';
}
