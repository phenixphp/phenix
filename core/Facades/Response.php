<?php

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static \Amp\Http\Server\Response plain(string $content, string $status = \Amp\Http\Status::OK)
 * @method static \Amp\Http\Server\Response json(string $content, string $status = \Amp\Http\Status::OK)
 */
class Response extends Facade
{
    protected static function getKeyName(): string
    {
        return 'response';
    }
}
