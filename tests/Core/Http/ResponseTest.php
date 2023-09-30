<?php

declare(strict_types=1);

use Amp\Http\Server\Response as ServerResponse;
use Core\Data\Collection;
use Core\Http\Response;

it('responds plain text', function () {
    $response = new Response();

    $serverResponse = $response->plain('Hello world!');

    expect($serverResponse)->toBeInstanceOf(ServerResponse::class);
    expect($serverResponse->getBody()->read())->toBe('Hello world!');
});

it('responds json data from plain array', function () {
    $data = ['name' => 'John Doe'];

    $response = new Response();

    $serverResponse = $response->json($data);

    expect($serverResponse)->toBeInstanceOf(ServerResponse::class);
    expect($serverResponse->getBody()->read())->toContain(json_encode($data));
});

it('responds json data from arrayable', function () {
    $data = ['name' => 'John Doe'];

    $collection = new Collection('array');
    $collection->add($data);

    $response = new Response();

    $serverResponse = $response->json($collection);

    expect($serverResponse)->toBeInstanceOf(ServerResponse::class);
    expect($serverResponse->getBody()->read())->toContain(json_encode($data));
});
