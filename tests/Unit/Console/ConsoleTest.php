<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

it('creates controller successfully', function () {
    $command = $this->phenix('make:controller', [
        'name' => 'TestController',
    ]);

    $command->assertCommandIsSuccessful();
});
