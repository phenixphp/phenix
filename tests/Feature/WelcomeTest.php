<?php

declare(strict_types=1);

it('responses successfully', function () {
    get('/')
        ->assertOk()
        ->assertBodyContains('Hello, world!');
});

it('responses not acceptable request', function () {
    get(path: '/', headers: ['Accept' => 'text/html'])
        ->assertNotAcceptable();
});
