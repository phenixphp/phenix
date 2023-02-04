<?php

declare(strict_types=1);

namespace Tests\Core\Console;

use Core\Contracts\Filesystem\File;

it('creates middleware successfully', function () {
    $mock = mock(File::class)->expect(
        exists: fn (string $path) => false,
        get: fn (string $path) => '',
        put: function (string $path) {
            expect($path)->toBe(base_path('app/Http/Middleware/AwesomeMiddleware.php'));

            return true;
        },
        createDirectory: function (string $path): void {
            // ..
        }
    );

    $this->app->swap(File::class, $mock);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:middleware', [
        'name' => 'AwesomeMiddleware',
    ]);

    $command->assertCommandIsSuccessful();

    expect($command->getDisplay())->toContain('Middleware successfully generated!');
});

it('does not create the middleware because it already exists', function () {
    $mock = mock(File::class)->expect(
        exists: fn (string $path) => true,
    );

    $this->app->swap(File::class, $mock);

    $this->phenix('make:middleware', [
        'name' => 'TestMiddleware',
    ]);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:middleware', [
        'name' => 'TestMiddleware',
    ]);

    $command->assertCommandIsSuccessful();

    expect($command->getDisplay())->toContain('Middleware already exists!');
});

it('creates middleware successfully with force option', function () {
    $tempDir = sys_get_temp_dir();
    $tempPath = $tempDir . DIRECTORY_SEPARATOR . 'TestMiddleware.php';

    file_put_contents($tempPath, 'old content');

    $this->assertEquals('old content', file_get_contents($tempPath));

    $mock = mock(File::class)->expect(
        exists: fn (string $path) => false,
        get: fn (string $path) => 'new content',
        put: fn (string $path, string $content) => file_put_contents($tempPath, $content),
        createDirectory: function (string $path): void {
            // ..
        }
    );

    $this->app->swap(File::class, $mock);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:middleware', [
        'name' => 'TestMiddleware',
        '--force' => true,
    ]);

    $command->assertCommandIsSuccessful();

    expect($command->getDisplay())->toContain('Middleware successfully generated!');
    expect('new content')->toBe(file_get_contents($tempPath));
});

it('creates middleware successfully in nested namespace', function () {
    $mock = mock(File::class)->expect(
        exists: fn (string $path) => false,
        get: fn (string $path) => '',
        put: function (string $path) {
            expect($path)->toBe(base_path('app/Http/Middleware/Admin/TestMiddleware.php'));

            return true;
        },
        createDirectory: function (string $path): void {
            // ..
        }
    );

    $this->app->swap(File::class, $mock);

    /** @var \Symfony\Component\Console\Tester\CommandTester $command */
    $command = $this->phenix('make:middleware', [
        'name' => 'Admin/TestMiddleware',
    ]);

    $command->assertCommandIsSuccessful();

    expect($command->getDisplay())->toContain('Middleware successfully generated!');
});
