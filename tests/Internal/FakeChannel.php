<?php

declare(strict_types=1);

namespace Tests\Internal;

use Amp\Cancellation;
use Amp\Sync\Channel;
use Closure;

class FakeChannel implements Channel
{
    public function receive(Cancellation|null $cancellation = null): mixed
    {
        return true;
    }

    public function send(mixed $data): void
    {
        // Intentionally empty: tasks running inside the test context do not send data through the channel.
    }

    public function close(): void
    {
        // Intentionally empty: there is nothing to release when the fake channel is closed.
    }

    public function isClosed(): bool
    {
        return false;
    }

    public function onClose(Closure $onClose): void
    {
        // Intentionally empty: the fake channel is never closed during the test context.
    }
}
