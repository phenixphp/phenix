<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Tests\Unit\Concerns\InteractWithConsole;

uses(InteractWithConsole::class);

it('creates controller successfully', function () {
    $command = $this->call('make:controller', [
        'name' => 'TestController',
    ]);

    $command->assertCommandIsSuccessful();
});
