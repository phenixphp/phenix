<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

/** @var \Phenix\Contracts\App $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->run();
