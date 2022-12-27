<?php

declare(strict_types=1);

use Core\AppBuilder;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->load();

return AppBuilder::build();
