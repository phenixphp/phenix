<?php

declare(strict_types=1);

use function Pest\Faker\faker;
use Phenix\Testing\Concerns\InteractWithDatabase;

uses(InteractWithDatabase::class);

it('registers a user', function (): void {
    $data = [
        'name' => faker()->name(),
        'email' => faker()->email(),
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = post('/register', $data);

    $response->assertCreated()
        ->assertJsonContains([
            'name' => $data['name'],
            'email' => $data['email'],
        ], 'data');

    $this->assertDatabaseHas('users', [
        'email' => $data['email'],
    ]);
});
