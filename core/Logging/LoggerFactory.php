<?php

declare(strict_types=1);

namespace Core\Logging;

use Amp\ByteStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Core\Contracts\Makeable;
use Core\Exceptions\RuntimeError;
use Core\Facades\File;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class LoggerFactory implements Makeable
{
    public static function make(string $key): Logger
    {
        $logHandler = match ($key) {
            'file' => self::fileHandler(),
            'stream' => self::streamHandler(),
            default => throw new RuntimeError("Unsupported logging channel: {$key}")
        };

        $logger = new Logger('phenix');
        $logger->pushHandler($logHandler);

        return $logger;
    }

    private static function streamHandler(): StreamHandler
    {
        $logHandler = new StreamHandler(ByteStream\getStdout());
        $logHandler->pushProcessor(new PsrLogMessageProcessor());
        $logHandler->setFormatter(new ConsoleFormatter());

        return $logHandler;
    }

    private static function fileHandler(): StreamHandler
    {
        $file = File::openFile(base_path('storage/framework/logs/phenix.log'), 'a');

        $logHandler = new StreamHandler($file);
        $logHandler->pushProcessor(new PsrLogMessageProcessor());
        $logHandler->setFormatter(new LineFormatter());

        return $logHandler;
    }
}
