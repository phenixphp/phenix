<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Util\Arr;

class Having extends Clause
{
    public function __construct()
    {
        $this->clauses = [];
        $this->arguments = [];
    }

    public function toSql(): array
    {
        $clauses = Arr::implodeDeeply($this->prepareClauses($this->clauses));

        return ["HAVING {$clauses}", $this->arguments];
    }
}
