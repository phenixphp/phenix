<?php

declare(strict_types=1);

define('APP_PATH', dirname(__DIR__));

use Core\App;
use Dotenv\Dotenv;

(Dotenv::createImmutable(base_path()))->load();

return new App();
