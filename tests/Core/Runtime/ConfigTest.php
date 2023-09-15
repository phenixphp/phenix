<?php

declare(strict_types=1);

use Core\Runtime\Config;

it('can get environment configurations successfully', function () {
    $config = Config::build();

    expect($config->get('app.name'))->toBe('Phenix');
});

it('can set environment configurations successfully', function () {
    $config = Config::build();

    $config->set('app.name', 'PHPhenix');

    expect($config->get('app.name'))->toBe('PHPhenix');
});
