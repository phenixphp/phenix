<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

/** @var \Core\App $app */
$app = require_once __DIR__ . '/../core/bootstrap.php';

$app->run();