<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Core\Contracts\Filesystem\File;

it('creates controller successfully', function () {
    $mock = mock(File::class)->expect(
        exists: fn (string $path) => false,
        get: fn (string $path) => '',
        put: fn (string $path) => true,
    );

    $this->app->swap(File::class, $mock);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:controller', [
        'name' => 'TestController',
        '--force' => true,
    ]);

    $command->assertCommandIsSuccessful();

    $output = $command->getDisplay();

    $this->assertStringContainsString('Controller successfully generated!', $output);
});

it('does not create the controller because it already exists', function () {
    $mock = mock(File::class)->expect(
        exists: fn (string $path) => true,
        get: fn (string $path) => '',
        put: fn (string $path) => true,
    );

    $this->app->swap(File::class, $mock);

    $this->phenix('make:controller', [
        'name' => 'TestController',
        '--force' => true,
    ]);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:controller', [
        'name' => 'TestController',
    ]);

    $command->assertCommandIsSuccessful();

    $output = $command->getDisplay();

    $this->assertStringContainsString('Controller already exists!', $output);
});

it('creates controller successfully in nested namespace', function () {
    $mock = mock(File::class)->expect(
        exists: fn (string $path) => false,
        get: fn (string $path) => '',
        put: fn (string $path) => true,
        createDirectory: function (string $path): void {
            // ..
        }
    );

    $this->app->swap(File::class, $mock);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:controller', [
        'name' => 'Admin/UserController',
        '--force' => true,
    ]);

    $command->assertCommandIsSuccessful();

    $output = $command->getDisplay();

    $this->assertStringContainsString('Controller successfully generated!', $output);
});
