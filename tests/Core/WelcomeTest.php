<?php

declare(strict_types=1);

namespace Tests\Core;

beforeEach(function () {
    $this->app->run();
});

it('responses successfully', function () {
    get('/')
        ->assertOk()
        ->assertBodyContains('Hello, world!');
});
