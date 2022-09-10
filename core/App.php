<?php

namespace Core;

use Amp\Loop;
use Monolog\Logger;
use Amp\Log\StreamHandler;
use Amp\Http\Server\Router;
use Amp\Http\Server\Server;
use Amp\Log\ConsoleFormatter;
use Amp\Socket\Server as SocketServer;
use Amp\ByteStream\ResourceOutputStream;

class App
{
    private Logger $logger;
    private array $sockets;
    private Server $server;

    public function __construct(Router $router)
    {
        $this->setupSockets();
        $this->setupLogger();

        $this->server = new Server($this->sockets, $router, $this->logger);
    }

    public function setupSockets(): void
    {
        $this->sockets = [
            SocketServer::listen("0.0.0.0:1337"),
            SocketServer::listen("[::]:1337"),
        ];
    }

    private function setupLogger(): void
    {
        $logHandler = new StreamHandler(new ResourceOutputStream(STDOUT));
        $logHandler->setFormatter(new ConsoleFormatter());

        $this->logger = new Logger('server');
        $this->logger->pushHandler($logHandler);
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
}




