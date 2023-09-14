<?php

declare(strict_types=1);

use Amp\Log\ConsoleFormatter;
use Core\Exceptions\RuntimeError;
use Core\Logging\LoggerFactory;
use Monolog\Formatter\LineFormatter;

it('makes all supported logger channels', function (string $channel, string $formatter) {
    $logger = LoggerFactory::make($channel);

    /** @var \Amp\Log\StreamHandler $handler */
    $handler = $logger->getHandlers()[0];

    expect($handler->getFormatter())->toBeInstanceOf($formatter);
})->with([
    ['file', LineFormatter::class],
    ['stream', ConsoleFormatter::class],
]);

it('throws error on unsupported channel', function () {
    expect(function () {
        LoggerFactory::make('unsupported');
    })->toThrow(RuntimeError::class);
});
