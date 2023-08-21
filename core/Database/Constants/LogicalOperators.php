<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum LogicalOperators: string
{
    case AND = 'AND';
    case OR = 'OR';
    case NOT = 'NOT';
}
