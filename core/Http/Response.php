<?php

declare(strict_types=1);

namespace Core\Http;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Response as ServerResponse;

class Response
{
    public function plain(string $content, int $status = HttpStatus::OK): ServerResponse
    {
        return new ServerResponse($status, ['content-type' => 'text/plain'], $content);
    }

    /**
     * @param array<string|int, array|string|int|bool> $content
     */
    public function json(array $content, int $status = HttpStatus::OK): ServerResponse
    {
        $body = json_encode(['data' => $content]);

        return new ServerResponse($status, ['content-type' => 'application/javascript'], $body . PHP_EOL);
    }
}
