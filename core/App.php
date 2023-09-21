<?php

declare(strict_types=1);

namespace Core;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\Router;
use Amp\Http\Server\SocketHttpServer;
use Amp\Socket;
use Core\Console\Phenix;
use Core\Contracts\App as AppContract;
use Core\Contracts\Makeable;
use Core\Facades\Config;
use Core\Facades\Route;
use Core\Logging\LoggerFactory;
use League\Container\Container;
use Monolog\Logger;

class App implements AppContract, Makeable
{
    private static string $path;
    private static Container $container;
    private static string|null $logginChannel = null;

    private Router $router;
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
        self::$container->add(
            Config::getKeyName(),
            \Core\Runtime\Config::build(...)
        )->setShared(true);

        /** @var array $providers */
        $providers = Config::get('app.providers');

        foreach ($providers as $provider) {
            self::$container->addServiceProvider(new $provider());
        }

        /** @var string $channel */
        $channel = self::$logginChannel ?? Config::get('logging.default');

        $this->logger = LoggerFactory::make($channel);

        $this->server = SocketHttpServer::createForDirectAccess($this->logger);

        self::$container->add(Phenix::class)->addMethodCall('registerCommands');
    }

    public function setRouter(): void
    {
        $this->router = new Router($this->server, $this->logger, $this->errorHandler);

        /** @var array $routes */
        $routes = self::$container->get(Route::getKeyName())->toArray();

        foreach ($routes as $route) {
            [$method, $path, $closure, $middlewares] = $route;

            $this->router->addRoute(
                $method->value,
                $path,
                Middleware\stackMiddleware($closure, ...$middlewares)
            );
        }

            foreach ($middlewares as $middleware) {
                $this->router->addMiddleware($middleware);
            }
        }
    }

    public function run(): void
    {
        /** @var int $port */
        $port = Config::get('app.port');

        [$ipv4, $ipv6] = Config::get('app.url');

        $this->server->expose(new Socket\InternetAddress($ipv4, $port));
        $this->server->expose(new Socket\InternetAddress($ipv6, $port));

        $this->server->start($this->router, $this->errorHandler);

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

    public static function setLoggingChannel(string $channel): void
    {
        self::$logginChannel = $channel;
    }

    public function swap(string $key, object $concrete): void
    {
        self::$container->extend($key)->setConcrete($concrete);
    }

    public function disableSignalTrapping(): void
    {
        $this->signalTrapping = false;
    }
}
