<?php

define('APP_PATH', dirname(__DIR__));

use Core\App;
use Core\Container;
use Dotenv\Dotenv;
use Core\Util\Files;

(Dotenv::createImmutable(base_path()))->load();

Container::build();

foreach (Files::directory(base_path('routes')) as $file) {
    require_once $file;
}

$app = new App((Container::get('router'))->getRouter());

return $app;