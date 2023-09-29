<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum Operators: string
{
    case EQUAL = '=';
    case DISTINCT = '!=';
    case GREATHER_THAN = '>';
    case GREATHER_THAN_OR_EQUAL = '>=';
    case LESS_THAN = '<';
    case LESS_THAN_OR_EQUAL = '<=';
    case IN = 'IN';
    case NOT_IN = 'NOT IN';
    case IS_TRUE = 'IS TRUE';
    case IS_FALSE = 'IS FALSE';
    case IS_NOT_NULL = 'IS NOT NULL';
    case IS_NULL = 'IS NULL';
    case LIKE = 'LIKE';
    case BETWEEN = 'BETWEEN';
    case NOT_BETWEEN = 'NOT BETWEEN';
    case EXISTS = 'EXISTS';
    case NOT_EXISTS = 'NOT EXISTS';
    case GROUP_BY = 'GROUP BY';
    case ORDER_BY = 'ORDER BY';
    case LIMIT = 'LIMIT';
    case OFFSET = 'OFFSET';
    case ALL = 'ALL';
    case ANY = 'ANY';
    case SOME = 'SOME';
}
