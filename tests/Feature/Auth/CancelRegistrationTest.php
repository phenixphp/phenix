<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use Phenix\Facades\Hash;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class CancelRegistrationTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_cancels_a_pending_unverified_registration(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        $response = $this->post('/register/cancel', ['email' => $user->email]);

        $response->assertOk()
            ->assertJsonPath('message', trans('auth.registration.cancelled'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('user_one_time_passwords', ['id' => $otp->id]);
    }

    /** @test */
    public function it_responds_unprocessable_when_email_does_not_exist(): void
    {
        $response = $this->post('/register/cancel', ['email' => 'nonexistent@example.com']);

        $response->assertUnprocessableEntity();
    }

    /** @test */
    public function it_responds_unprocessable_when_email_is_already_verified(): void
    {
        User::create([
            'name' => $this->faker()->name(),
            'email' => 'verified@example.com',
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $response = $this->post('/register/cancel', ['email' => 'verified@example.com']);

        $response->assertUnprocessableEntity();
    }

    /** @test */
    public function it_rejects_authenticated_users_via_guest_middleware(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $token = $user->createToken('auth_token');

        $this->post(
            path: '/register/cancel',
            body: ['email' => $user->email],
            headers: ['Authorization' => 'Bearer ' . $token->toString()]
        )->assertUnauthorized();
    }
}
