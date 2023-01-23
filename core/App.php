<?php

declare(strict_types=1);

namespace Core;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\StreamHandler;
use Amp\Socket;
use Core\Console\Phenix;
use Core\Contracts\App as AppContract;
use Core\Contracts\Makeable;
use Core\Facades\Config;
use Core\Facades\File;
use Core\Routing\Router;
use Core\Util\Directory;
use Core\Util\NamespaceResolver;
use League\Container\Container;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class App implements AppContract, Makeable
{
    private static string $path;
    private static Container $container;

    private Logger $logger;
    private SocketHttpServer $server;
    private bool $signalTrapping = true;
    private DefaultErrorHandler $errorHandler;

    public function __construct(string $path)
    {
        self::$path = $path;
        self::$container = new Container();

        $this->errorHandler = new DefaultErrorHandler();
    }

    public function setup(): void
    {
        $this->registerElementalFacades();

        $this->setupLogger();

        $this->setupServer();

        $this->setupDefinitions();
    }

    public function run(): void
    {
        $this->server->start(self::$container->get('router')->getRouter(), $this->errorHandler);

        if ($this->signalTrapping) {
            $signal = \Amp\trapSignal([SIGINT, SIGTERM]);

            $this->logger->info("Caught signal {$signal}, stopping server");

            $this->stop();
        }
    }

    public function stop(): void
    {
        $this->server->stop();
    }

    public static function make(string $key): object
    {
        return self::$container->get($key);
    }

    public static function path(): string
    {
        return self::$path;
    }

    public function swap(string $key, object $concrete): void
    {
        self::$container->extend($key)->setConcrete($concrete);
    }

    public function disableSignalTrapping(): void
    {
        $this->signalTrapping = false;
    }

    private function setupLogger(): void
    {
        $file = File::openFile(base_path('storage/framework/logs/phenix.log'), 'a');

        $logHandler = new StreamHandler($file);
        $logHandler->pushProcessor(new PsrLogMessageProcessor());
        $logHandler->setFormatter(new LineFormatter());

        $this->logger = new Logger('phenix');
        $this->logger->pushHandler($logHandler);
    }

    private function setupServer(): void
    {
        $this->server = new SocketHttpServer($this->logger);

        /** @var int $port */
        $port = Config::get('app.port');

        [$ipv4, $ipv6] = Config::get('app.url');

        $this->server->expose(new Socket\InternetAddress($ipv4, $port));
        $this->server->expose(new Socket\InternetAddress($ipv6, $port));
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
            \Core\Facades\Router::getKeyName(),
            fn () => new Router($this->server, $this->errorHandler)
        )->setShared(true);
    }

    private function registerElementalFacades(): void
    {
        self::$container->add(
            Config::getKeyName(),
            \Core\Runtime\Config::build(...)
        )->setShared(true);

        self::$container->add(
            \Core\Facades\Storage::getKeyName(),
            \Core\Filesystem\Storage::class
        );

        self::$container->add(
            File::getKeyName(),
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
