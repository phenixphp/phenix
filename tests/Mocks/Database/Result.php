<?php

declare(strict_types=1);

namespace Tests\Mocks\Database;

use Amp\Sql\Result as SqlResult;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Result implements SqlResult, IteratorAggregate
{
    protected int $count;
    protected ArrayIterator $fakeResult;

    public function __construct(array $fakeResult = [])
    {
        $this->count = count($fakeResult);
        $this->fakeResult = new ArrayIterator($fakeResult);
    }

    public function fetchRow(): ?array
    {
        return $this->fakeResult->current();
    }

    public function getNextResult(): ?self
    {
        return $this;
    }

    public function getRowCount(): ?int
    {
        return $this->count;
    }

    public function getColumnCount(): ?int
    {
        return 3;
    }

    public function getIterator(): Traversable
    {
        return $this->fakeResult;
    }
}
