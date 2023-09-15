<?php

declare(strict_types=1);

use Core\Contracts\Filesystem\File;
use Tests\Util\Mock;

it('creates migration successfully', function () {
    $mock = Mock::of(File::class)->expect(
        exists: fn (string $path) => false,
        get: fn (string $path) => '',
        put: fn (string $path) => true,
        createDirectory: function (string $path): void {
            // ..
        }
    );

    $this->app->swap(File::class, $mock);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:migration', [
        'name' => 'CreateProductsTable',
    ]);

    $command->assertCommandIsSuccessful();

    expect($command->getDisplay())->toContain('Migration successfully generated!');
});
