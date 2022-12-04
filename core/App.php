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
use Core\Contracts\Filesystem\File as FileContract;
use Core\Filesystem\File;
use Core\Filesystem\Storage;
use Core\Http\Response;
use Core\Routing\Router;
use Core\Runtime\Config;
use Core\Util\Files;
use League\Container\Container;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class App
{
    private Logger $logger;
    private SocketHttpServer $server;
    private static Container $container;
    private DefaultErrorHandler $errorHandler;

    public function __construct()
    {
        $this->setupLogger();
        self::$container = new Container();
        $this->errorHandler = new DefaultErrorHandler();

        $this->server = new SocketHttpServer($this->logger);
        $this->server->expose(new Socket\InternetAddress("0.0.0.0", 1337));
        $this->server->expose(new Socket\InternetAddress("[::]", 1337));

        $this->setupDefinitions();
        $this->loadRoutes();
    }

    public function run(): void
    {
        $this->server->start(self::$container->get('router')->getRouter(), $this->errorHandler);

        $signal = \Amp\trapSignal([SIGINT, SIGTERM]);

        $this->logger->info("Caught signal $signal, stopping server");

        $this->server->stop();
    }

    public static function make(string $key): mixed
    {
        return self::$container->get($key);
    }

    public function swap(string $key, object $concrete): void
    {
        self::$container->extend($key)->setConcrete($concrete);
    }

    private function loadRoutes(): void
    {
        foreach (Files::directory(base_path('routes')) as $file) {
            require_once $file;
        }
    }

    private function setupLogger(): void
    {
        $logHandler = new StreamHandler(ByteStream\getStdout());
        $logHandler->pushProcessor(new PsrLogMessageProcessor());
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
        self::$container->add('router', function () {
            return new Router($this->server, $this->errorHandler);
        })->setShared(true);

        self::$container->add('response', Response::class);
        self::$container->add('storage', Storage::class);
        self::$container->add('config', Config::build(...))->setShared(true);
        self::$container->add(FileContract::class, File::class);
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
