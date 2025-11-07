<?php

declare(strict_types=1);

use App\Mail\SendEmailVerificationOtp;
use Phenix\Facades\Mail;
use Phenix\Testing\Concerns\RefreshDatabase;

use function Pest\Faker\faker;

uses(RefreshDatabase::class);

it('registers a user', function (): void {
    Mail::fake();

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

    Mail::expect(SendEmailVerificationOtp::class)->toBeSent();
});
