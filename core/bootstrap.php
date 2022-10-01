<?php

declare(strict_types=1);

if (! defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

use Core\App;
use Dotenv\Dotenv;

(Dotenv::createImmutable(base_path()))->load();

return new App();
