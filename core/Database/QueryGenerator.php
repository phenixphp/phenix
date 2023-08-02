<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Database\Concerns\Query\BuildsQuery;
use Core\Database\Concerns\Query\HasJoinClause;

class QueryGenerator extends QueryBase
{
    use BuildsQuery;
    use HasJoinClause;
}
