<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use Phenix\Facades\Cache;
use Phenix\Facades\Hash;
use Phenix\Http\Constants\HttpStatus;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class LoginAuthorizationTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_authorizes_login_and_returns_a_bearer_token(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::LOGIN);

        $response = $this->post(route('login.authorize'), [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer');

        $data = $response->getDecodedBody();

        $this->assertNotEmpty($data['access_token'] ?? null);
        $this->assertNotEmpty($data['expires_at'] ?? null);

        $this->assertDatabaseHas('user_one_time_passwords', [
            'id' => $otp->id,
            'used_at' => Date::now(),
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token',
            'token' => hash('sha256', $data['access_token']),
        ]);
    }

    /** @test */
    public function it_responds_not_found_for_non_existing_otp(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $response = $this->post(route('login.authorize'), [
            'email' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_responds_not_found_when_otp_has_different_scope(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        $response = $this->post(route('login.authorize'), [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_responds_not_found_when_otp_is_already_used(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::LOGIN);
        $otp->usedAt = Date::now();
        $otp->save();

        $response = $this->post(route('login.authorize'), [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_responds_not_found_when_otp_is_expired(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::LOGIN);

        Date::setTestNow(Date::now()->addMinutes(11));

        $response = $this->post(route('login.authorize'), [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_rate_limits_login_authorization_attempts_per_client(): void
    {
        Cache::clear();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.authorize'), [
                'email' => $user->email,
                'otp' => '123456',
            ])->assertNotFound();
        }

        $this->post(route('login.authorize'), [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatusCode(HttpStatus::TOO_MANY_REQUESTS)
            ->assertJsonPath('message', trans('auth.rate_limit.exceeded'));
    }

    /** @test */
    public function it_uses_independent_rate_limit_buckets_for_login_and_login_authorization(): void
    {
        Cache::clear();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::LOGIN);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'WrongPass99',
            ])->assertUnauthorized();
        }

        $this->post(route('login.authorize'), [
            'email' => $user->email,
            'otp' => $otp->otp,
        ])->assertOk()
            ->assertJsonPath('token_type', 'Bearer');
    }
}
