<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use Phenix\Facades\Crypto;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_verifies_email(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', trans('auth.email_verification.verified'));

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => Date::now(),
        ]);

        $this->assertDatabaseHas('user_one_time_passwords', [
            'user_id' => $user->id,
            'scope' => OneTimePasswordScope::VERIFY_EMAIL->value,
            'used_at' => Date::now(),
        ]);
    }

    /** @test */
    public function it_does_not_verify_email_because_email_is_already_verified(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
            'email_verified_at' => Date::now(),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('errors.email.0', trans('validation.exists', ['field' => 'email']));
    }

    /** @test */
    public function it_responds_not_found_for_non_existing_otp(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
        ]);

        $response = $this->post('/verify-email', [
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
            'password' => Crypto::encryptString('password'),
        ]);

        // Create OTP with LOGIN scope instead of VERIFY_EMAIL
        $otp = $user->createOneTimePassword(OneTimePasswordScope::LOGIN);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => null,
        ]);
    }

    /** @test */
    public function it_responds_not_found_when_otp_is_already_used(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        // Mark the OTP as already used
        $otp->usedAt = Date::now();
        $otp->save();

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));

        // User should not be verified
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => null,
        ]);
    }

    /** @test */
    public function it_responds_not_found_when_otp_is_expired(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        // Advance time by 11 minutes (default expiration is 10 minutes)
        Date::setTestNow(Date::now()->addMinutes(11));

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));

        // User should not be verified
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => null,
        ]);
    }
}
