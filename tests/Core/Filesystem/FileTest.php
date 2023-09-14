<?php

declare(strict_types=1);

use Amp\File\File as FileHandler;
use Core\Filesystem\File;

beforeEach(function () {
    $path = sys_get_temp_dir() . '/file.txt';

    if (file_exists($path)) {
        unlink($path);
    }
});

it('reads files successfully', function () {
    $path = sys_get_temp_dir() . '/file.txt';

    file_put_contents($path, 'php');

    $file = new File();

    expect($file->get($path))->toBe('php');
});

it('writes files successfully', function () {
    $path = sys_get_temp_dir() . '/file.txt';

    $file = new File();
    $file->put($path, 'php');

    expect(file_get_contents($path))->toBe('php');
});

it('checks if file exists', function () {
    $path = sys_get_temp_dir() . '/file.txt';

    file_put_contents($path, 'php');

    $file = new File();

    expect($file->exists($path))->toBeTrue();
});

it('checks if path exist and is it a directory', function () {
    $file = new File();

    expect($file->isDirectory(sys_get_temp_dir()))->toBeTrue();
});

it('checks if path exist and is it a file', function () {
    $path = sys_get_temp_dir() . '/file.txt';

    file_put_contents($path, 'php');

    $file = new File();

    expect($file->isFile($path))->toBeTrue();
});

it('creates a directory successfully', function () {
    $path = sys_get_temp_dir() . '/php';

    if (file_exists($path)) {
        rmdir($path);
    }

    $file = new File();
    $file->createDirectory($path);

    expect(file_exists($path))->toBeTrue();
});

it('open a file for IO operations', function () {
    $path = sys_get_temp_dir() . '/file.txt';

    file_put_contents($path, 'php');

    $file = new File();

    expect($file->openFile($path))->toBeInstanceOf(FileHandler::class);
});
