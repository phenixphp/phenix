<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum Actions
{
    case SELECT;
    case SELECT_EXISTS;
    case INSERT;
    case UPDATE;
    case DELETE;
}
