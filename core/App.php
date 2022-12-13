<?php

declare(strict_types=1);

namespace Core;

use Amp\ByteStream;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Socket;
use Core\Console\Phenix;
use Core\Contracts\App as AppContract;
use Core\Contracts\Makeable;
use Core\Facades\Config;
use Core\Routing\Router;
use Core\Util\Directory;
use Core\Util\NamespaceResolver;
use League\Container\Container;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class App implements AppContract, Makeable
{
    private Logger $logger;
    private SocketHttpServer $server;
    private static Container $container;
    private DefaultErrorHandler $errorHandler;

    public function __construct()
    {
        $logHandler = new StreamHandler(ByteStream\getStdout());
        $logHandler->pushProcessor(new PsrLogMessageProcessor());
        $logHandler->setFormatter(new ConsoleFormatter());

        $this->logger = new Logger('server');
        $this->logger->pushHandler($logHandler);

        self::$container = new Container();
        $this->errorHandler = new DefaultErrorHandler();

        $this->server = new SocketHttpServer($this->logger);
    }

    public function setup(): void
    {
        $this->setupDefinitions();

        /** @var int $port */
        $port = Config::get('app.port');

        [$ipv4, $ipv6] = Config::get('app.url');
        dump($ipv4, $ipv6);
        $this->server->expose(new Socket\InternetAddress($ipv4, $port));
        $this->server->expose(new Socket\InternetAddress($ipv6, $port));
    }

    public function run(): void
    {
        $this->server->start(self::$container->get('router')->getRouter(), $this->errorHandler);

        $signal = \Amp\trapSignal([SIGINT, SIGTERM]);

        $this->logger->info("Caught signal $signal, stopping server");

        $this->server->stop();
    }

    public static function make(string $key): object
    {
        return self::$container->get($key);
    }

    public function swap(string $key, object $concrete): void
    {
        self::$container->extend($key)->setConcrete($concrete);
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
        self::$container->add(
            Config::getKeyName(),
            \Core\Runtime\Config::build(...)
        )->setShared(true);

        self::$container->add(
            \Core\Facades\Router::getKeyName(),
            fn () => new Router($this->server, $this->errorHandler)
        )->setShared(true);

        self::$container->add(
            \Core\Facades\Storage::getKeyName(),
            \Core\Filesystem\Storage::class
        );

        self::$container->add(
            \Core\Facades\File::getKeyName(),
            \Core\Filesystem\File::class
        );
    }

    private function registerControllers(): void
    {
        $controllers = Directory::all(self::getControllersPath());

        foreach ($controllers as $controller) {
            $controller = NamespaceResolver::parse($controller);

            self::$container->add($controller);
        }
    }

    private function getControllersPath(): string
    {
        return base_path('app'. DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers');
    }
}
