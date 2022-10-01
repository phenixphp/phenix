<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Core\Contracts\Filesystem\File;
use Tests\Mocks\File as FileMock;

it('creates controller successfully', function () {
    $this->app->swap(File::class, new FileMock());

    $command = $this->phenix('make:controller', [
        'name' => 'TestController',
    ]);

    $command->assertCommandIsSuccessful();
});
