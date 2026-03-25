<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Mail\SendEmailVerificationOtp;
use Phenix\Facades\Mail;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user(): void
    {
        Mail::fake();

        $data = [
            'name' => $this->faker()->name(),
            'email' => $this->faker()->email(),
            'password' => 'P@ssw0rd',
            'password_confirmation' => 'P@ssw0rd',
        ];

        $response = $this->post(route('register'), $data);

        $response->assertCreated()
            ->assertJsonContains([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);

        $data = $response->getDecodedBody();

        $this->assertDatabaseHas('user_one_time_passwords', [
            'user_id' => $data['id'],
            'scope' => OneTimePasswordScope::VERIFY_EMAIL->value,
        ]);

        Mail::expect(SendEmailVerificationOtp::class)->toBeSent();
    }
}
