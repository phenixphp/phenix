<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Core\Contracts\Filesystem\File;
use Tests\Mocks\File as FileMock;

it('creates controller successfully', function () {
    $this->app->swap(File::class, new FileMock());

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
    $this->app->swap(File::class, new FileMock());

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
