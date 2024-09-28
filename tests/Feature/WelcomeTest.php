<?php

declare(strict_types=1);

it('responses successfully', function () {
    get('/')
        ->assertOk()
        ->assertBodyContains('Hello, world!');
});
