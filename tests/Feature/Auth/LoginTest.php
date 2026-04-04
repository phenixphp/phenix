<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Mail\LoginOtp;
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

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'P@ssw0rd12',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', trans('auth.otp.login.sent'));

        $this->assertDatabaseHas('user_one_time_passwords', [
            'user_id' => $user->id,
            'scope' => OneTimePasswordScope::LOGIN->value,
        ]);

        Mail::expect(LoginOtp::class)->toBeSentTimes(1);
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

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'WrongPass99',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', trans('auth.login.invalid_credentials'));

        $this->assertSame(
            0,
            UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::LOGIN->value)
                ->count()
        );

        Mail::expect(LoginOtp::class)->toNotBeSent();
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

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'P@ssw0rd12',
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('errors.email.0', trans('validation.exists', ['field' => 'email']));

        Mail::expect(LoginOtp::class)->toNotBeSent();
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

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'P@ssw0rd12',
        ]);

        $response->assertStatusCode(HttpStatus::TOO_MANY_REQUESTS)
            ->assertJsonPath('message', trans('auth.otp.limit_exceeded'));

        $this->assertSame(
            5,
            UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::LOGIN->value)
                ->count()
        );

        Mail::expect(LoginOtp::class)->toNotBeSent();
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
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'WrongPass99',
            ])->assertUnauthorized();
        }

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'WrongPass99',
        ])->assertStatusCode(HttpStatus::TOO_MANY_REQUESTS)
            ->assertJsonPath('message', trans('auth.rate_limit.exceeded'));

        Mail::expect(LoginOtp::class)->toNotBeSent();
    }
}
