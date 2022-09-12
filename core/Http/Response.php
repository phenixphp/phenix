<?php

namespace Core\Http;

use Amp\Http\Server\Response as ServerResponse;
use Amp\Http\Status;

class Response
{
    public function plain(string $content, string $status = Status::OK): ServerResponse
    {
        return new ServerResponse($status, ['content-type' => 'text/plain'], $content);
    }

    public function json(array $content, string $status = Status::OK): ServerResponse
    {
        return new ServerResponse($status, ['content-type' => 'application/javascript'], json_encode($content));
    }
}
