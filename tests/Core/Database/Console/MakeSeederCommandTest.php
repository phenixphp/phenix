<?php

declare(strict_types=1);

use Core\Contracts\Filesystem\File;
use Tests\Util\Mock;

it('creates seeder successfully', function () {
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
    $command = $this->phenix('make:seeder', [
        'name' => 'UsersSeeder',
    ]);

    $command->assertCommandIsSuccessful();

    expect($command->getDisplay())->toContain('Seeder successfully generated!');
});
