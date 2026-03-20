<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Mail\SendLoginOtp;
use App\Models\User;
use App\Models\UserOtp;
use Phenix\Facades\Cache;
use Phenix\Facades\Hash;
use Phenix\Facades\Mail;
use Phenix\Http\Constants\HttpStatus;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_sends_a_login_otp_for_valid_verified_credentials(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'P@ssw0rd12',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'A verification code has been sent to your email address.');

        $this->assertDatabaseHas('user_one_time_passwords', [
            'user_id' => $user->id,
            'scope' => OneTimePasswordScope::LOGIN->value,
        ]);

        Mail::expect(SendLoginOtp::class)->toBeSentTimes(1);
    }

    /** @test */
    public function it_rejects_wrong_password(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'WrongPass99',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid credentials.');

        $this->assertSame(
            0,
            UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::LOGIN->value)
                ->count()
        );

        Mail::expect(SendLoginOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_rejects_unverified_email(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'P@ssw0rd12',
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('errors.email.0', 'The selected email is invalid.');

        Mail::expect(SendLoginOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_responds_too_many_requests_when_login_otp_limit_is_exceeded(): void
    {
        Date::setTestNow(Date::now());
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $user->createOneTimePassword(OneTimePasswordScope::LOGIN);
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'P@ssw0rd12',
        ]);

        $response->assertStatusCode(HttpStatus::TOO_MANY_REQUESTS)
            ->assertJsonPath('message', 'You have exceeded the maximum number of OTP requests. Please try again later.');

        $this->assertSame(
            5,
            UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::LOGIN->value)
                ->count()
        );

        Mail::expect(SendLoginOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_responds_unauthorized_when_authorization_token_is_present(): void
    {
        Mail::fake();

        $response = $this->post(
            '/login',
            [
                'email' => $this->faker()->freeEmail(),
                'password' => 'P@ssw0rd12',
            ],
            [],
            ['Authorization' => 'Bearer any-token']
        );

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthorized');

        Mail::expect(SendLoginOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_rate_limits_login_attempts_per_client(): void
    {
        Cache::clear();
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'WrongPass99',
            ])->assertUnauthorized();
        }

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'WrongPass99',
        ])->assertStatusCode(HttpStatus::TOO_MANY_REQUESTS)
            ->assertJsonPath('message', 'Rate limit exceeded. Please try again later.');

        Mail::expect(SendLoginOtp::class)->toNotBeSent();
    }
}
