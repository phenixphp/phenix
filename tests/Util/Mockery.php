<?php

declare(strict_types=1);

namespace Tests\Util;

use Mockery as MockeryBase;
use Mockery\MockInterface;

/**
 * @template TObject as object
 *
 * @mixin TObject|MockInterface
 */
class Mockery extends MockeryBase
{
    /**
     * The object being mocked.
     *
     * @readonly
     *
     * @var TObject|MockInterface
     */
    private object $mock;

    /**
     * Creates a new mock instance.
     *
     * @param  class-string<TObject>|TObject  $object
     */
    public function __construct(string|object $object)
    {
        /** @var TObject|MockInterface $mock */
        $mock = Mockery::mock($object);

        $this->mock = $mock;
    }

    /**
     * Define mock expectations.
     *
     * @return TObject|MockInterface
     */
    public function expect(callable ...$methods)
    {
        foreach ($methods as $method => $expectation) {
            /* @phpstan-ignore-next-line */
            $method = $this->mock
                ->shouldReceive((string) $method)
                ->atLeast()
                ->once();

            $method->andReturnUsing($expectation);
        }

        return $this->mock;
    }

    /**
     * Proxies calls to the original mock object.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        /* @phpstan-ignore-next-line */
        return $this->mock->{$method}(...$arguments);
    }
}
