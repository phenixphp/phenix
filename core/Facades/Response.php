<?php

namespace Core\Facades;

use Core\Runtime\Facade;

/**
 * @method static \Amp\Http\Server\Response plain(string $content, string $status = Status::OK)
 * @method static \Amp\Http\Server\Response json(string $content, string $status = Status::OK)
 */
class Response extends Facade
{
    public static function getKeyName(): string
    {
        return 'response';
    }
}
