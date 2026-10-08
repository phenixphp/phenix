<?php

declare(strict_types=1);

namespace Tests\Internal;

use Amp\Cancellation;
use Closure;

class FakeCancellation implements Cancellation
{
    public function subscribe(Closure $callback): string
    {
        return 'fake-cancellation-id';
    }

    public function unsubscribe(string $id): void
    {
        // Intentionally empty: no cancellation handlers are registered in the test context.
    }

    public function isRequested(): bool
    {
        return false;
    }

    public function throwIfRequested(): void
    {
        // Intentionally empty: cancellation is never requested in the test context.
    }
}
