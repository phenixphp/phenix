<?php

define('APP_PATH', dirname(__DIR__));

use Core\App;
use Dotenv\Dotenv;

(Dotenv::createImmutable(base_path()))->load();

$app = new App();

return $app;
