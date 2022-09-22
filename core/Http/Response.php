<?php

declare(strict_types=1);

namespace Core\Http;

use Amp\Http\Server\Response as ServerResponse;
use Amp\Http\Status;

class Response
{
    public function plain(string $content, int $status = Status::OK): ServerResponse
    {
        return new ServerResponse($status, ['content-type' => 'text/plain'], $content);
    }

    public function json(array $content, int $status = Status::OK): ServerResponse
    {
        return new ServerResponse($status, ['content-type' => 'application/javascript'], json_encode($content));
    }
}
