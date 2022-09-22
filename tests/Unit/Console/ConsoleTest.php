<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

it('creates controller successfully', function () {
    $this->app::swap(
        \Core\Contracts\Filesystem\File::class,
        \Tests\Mocks\File::class,
    );

    $command = $this->phenix('make:controller', [
        'name' => 'TestController',
    ]);

    $command->assertCommandIsSuccessful();
});
