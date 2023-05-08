<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum Joins: string
{
    case INNER = 'INNER JOIN';
    case LEFT = 'LEFT JOIN';
    case RIGHT = 'RIGHT JOIN';
    case CROSS = 'CROSS JOIN';
}
