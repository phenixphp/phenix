<?php

declare(strict_types=1);

namespace Core;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\HttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Socket\Server as SocketServer;
use Core\Console\Phenix;
use Core\Http\Response;
use Core\Routing\Router;
use Core\Runtime\Config;
use Core\Util\Files;
use League\Container\Container;
use Monolog\Logger;

class App
{
    /**
     * @var array<int, \Amp\Socket\Server>
     */
    private array $sockets;
    private Logger $logger;
    private HttpServer $server;
    private static Container $container;

    public function __construct()
    {
        $this->setupLogger();
        $this->setupSockets();

        self::$container = new Container();

        $this->setupDefinitions();
        $this->loadRoutes();

        $this->server = new HttpServer(
            $this->sockets,
            self::$container->get('router')->getRouter(),
            $this->logger
        );
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

    public static function make(string $key): mixed
    {
        return self::$container->get($key);
    }

    private function loadRoutes(): void
    {
        foreach (Files::directory(base_path('routes')) as $file) {
            require_once $file;
        }
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

    private function setupDefinitions(): void
    {
        $this->registerFacades();
        $this->registerControllers();

        self::$container->add(Phenix::class)
            ->addMethodCall('registerCommands');
    }

    private function registerFacades(): void
    {
        self::$container->add('response', Response::class);
        self::$container->add('router', Router::class)->setShared(true);
        self::$container->add('config', Config::build(...))->setShared(true);
    }

    private function registerControllers(): void
    {
        $controllers = Files::directory(self::getControllersPath());

        foreach ($controllers as $controller) {
            $controller = self::parseNamespace($controller);

            self::$container->add($controller);
        }
    }

    private function getControllersPath(): string
    {
        return base_path('app'. DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers');
    }

    private static function parseNamespace(string $namespace): string
    {
        $namespace = str_replace([APP_PATH . DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $namespace);

        return ucfirst($namespace);
    }
}
