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
    ]);

    $command->assertCommandIsSuccessful();

    $output = $command->getDisplay();

    $this->assertStringContainsString('Controller successfully generated!', $output);
});

it('does not create the controller because it already exists', function () {
    $mock = mock(File::class)->expect(
        exists: fn (string $path) => true,
    );

    $this->app->swap(File::class, $mock);

    $this->phenix('make:controller', [
        'name' => 'TestController',
    ]);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:controller', [
        'name' => 'TestController',
    ]);

    $command->assertCommandIsSuccessful();

    $output = $command->getDisplay();

    $this->assertStringContainsString('Controller already exists!', $output);
});

it('creates controller successfully with force option', function () {
    $tempDir = sys_get_temp_dir();
    $tempPath = $tempDir . DIRECTORY_SEPARATOR . 'TestController.php';

    file_put_contents($tempPath, 'old content');

    $this->assertEquals('old content', file_get_contents($tempPath));

    $mock = mock(File::class)->expect(
        exists: fn (string $path) => false,
        get: fn (string $path) => 'new content',
        put: fn (string $path, string $content) => file_put_contents($tempPath, $content),
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
    $this->assertEquals('new content', file_get_contents($tempPath));
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
    ]);

    $command->assertCommandIsSuccessful();

    $output = $command->getDisplay();

    $this->assertStringContainsString('Controller successfully generated!', $output);
});
