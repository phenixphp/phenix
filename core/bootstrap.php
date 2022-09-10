<?php

define('APP_PATH', dirname(__DIR__));

use Core\App;
use Core\Container;
use Core\Router;
use Dotenv\Dotenv;
use Core\Util\Files;

(Dotenv::createImmutable(base_path()))->load();

Router::init();

foreach (Files::directory(base_path('routes')) as $file) {
    require_once $file;
}

Container::build();

$app = new App(Router::getRouter());

return $app;