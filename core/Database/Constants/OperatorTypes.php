<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum OperatorTypes
{
    case ARITHMETIC;
    case BITWISE;
    case COMPARISON;
    case COMPOUND;
    case LOGICAL;
}
