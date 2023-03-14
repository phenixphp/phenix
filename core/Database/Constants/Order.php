<?php

declare(strict_types=1);

namespace Core\Database\Constants;

enum Order: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
