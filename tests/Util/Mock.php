<?php

declare(strict_types=1);

namespace Tests\Util;

class Mock
{
    /**
     * @template TObject as object
     *
     * @param class-string<TObject>|TObject $object
     *
     * @return Mockery<TObject>
     */
    public static function of(string|object $object): Mockery
    {
        return new Mockery($object);
    }
}
