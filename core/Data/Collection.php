<?php

declare(strict_types=1);

namespace Core\Data;

use Ramsey\Collection\Collection as GenericCollection;
use SplFixedArray;

class Collection extends GenericCollection
{
    public static function fromArray(array $data): self
    {
        $data = SplFixedArray::fromArray($data);
        $collection = new self('array');

        foreach ($data as $value) {
            $collection->add($value);
        }

        return $collection;
    }
}
