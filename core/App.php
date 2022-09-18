<?php

namespace Core;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Router;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Socket\Server as SocketServer;
use Monolog\Logger;

class App
{
    private Logger $logger;
    private array $sockets;
    private HttpServer $server;

    public function __construct(Router $router)
    {
        $this->setupLogger();
        $this->setupSockets();

        $this->server = new HttpServer($this->sockets, $router, $this->logger);

    }

    public function run(): void
    {
        Loop::run(function () {
            yield $this->server->start();

            Loop::onSignal(SIGINT, function (string $watcherId) {
                Loop::cancel($watcherId);

                yield $this->server->stop();
            });
        });
    }

    private function setupSockets(): void
    {
        $this->sockets = [
            SocketServer::listen('0.0.0.0:1337'),
            SocketServer::listen('[::]:1337'),
        ];
    }

    private function setupLogger(): void
    {
        $logHandler = new StreamHandler(new ResourceOutputStream(STDOUT));
        $logHandler->setFormatter(new ConsoleFormatter());

        $this->logger = new Logger('server');
        $this->logger->pushHandler($logHandler);
    }
}
