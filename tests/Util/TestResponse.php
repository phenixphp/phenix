<?php

declare(strict_types=1);

namespace Tests\Util;

use Amp\Http\Client\Response;
use Amp\Http\HttpStatus;

class TestResponse
{
    public readonly string $body;

    public function __construct(public Response $response)
    {
        $this->body = $response->getBody()->buffer();
    }

    public function assertOk(): self
    {
        expect($this->response->getStatus())->toBe(HttpStatus::OK);

        return $this;
    }

    public function assertNotAcceptable(): self
    {
        expect($this->response->getStatus())->toBe(HttpStatus::NOT_ACCEPTABLE);

        return $this;
    }

    /**
     * @param array<int, string>|string $needles
     * @return self
     */
    public function assertBodyContains(array|string $needles): self
    {
        $needles = (array) $needles;

        expect($this->body)->toContain(...$needles);

        return $this;
    }
}
