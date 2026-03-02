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
            'email' => 'test@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.message', 'Email verified successfully.');

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
            'email' => 'verified@gmail.com',
            'password' => Crypto::encryptString('password'),
            'email_verified_at' => Date::now(),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('data.errors.email.0', 'The selected email is invalid.');
    }

    /** @test */
    public function it_responds_not_found_for_non_existing_otp(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'nonexist@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('data.message', 'The provided OTP is invalid.');
    }

    /** @test */
    public function it_responds_not_found_when_otp_has_different_scope(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'scope@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        // Create OTP with LOGIN scope instead of VERIFY_EMAIL
        $otp = $user->createOneTimePassword(OneTimePasswordScope::LOGIN);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('data.message', 'The provided OTP is invalid.');

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => null,
        ]);
    }

    /** @test */
    public function it_responds_not_found_when_otp_belongs_to_different_user(): void
    {
        $userA = User::create([
            'name' => $this->faker()->name(),
            'email' => 'usera@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $userB = User::create([
            'name' => $this->faker()->name(),
            'email' => 'userb@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        // Create OTP for user A
        $otp = $userA->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        // Try to use user A's OTP with user B's email
        $response = $this->post('/verify-email', [
            'email' => $userB->email,
            'otp' => $otp->otp,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('data.message', 'The provided OTP is invalid.');

        // Neither user should be verified
        $this->assertDatabaseHas('users', [
            'email' => $userA->email,
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userB->email,
            'email_verified_at' => null,
        ]);
    }

    /** @test */
    public function it_responds_not_found_when_otp_is_already_used(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'used@gmail.com',
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
            ->assertJsonPath('data.message', 'The provided OTP is invalid.');

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
            'email' => 'expired@gmail.com',
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
            ->assertJsonPath('data.message', 'The provided OTP is invalid.');

        // User should not be verified
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => null,
        ]);
    }

    /** @test */
    public function it_verifies_email_when_otp_is_one_second_before_expiration(): void
    {
        $startTime = Date::now();
        Date::setTestNow($startTime);

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'boundary@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        // Advance time to 1 second before expiration (default is 10 minutes)
        Date::setTestNow($startTime->addMinutes(10)->subSecond());

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp->otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.message', 'Email verified successfully.');

        // User should be verified
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => Date::now(),
        ]);
    }

    /** @test */
    public function it_verifies_email_with_older_otp_when_multiple_valid_otps_exist(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'multiple@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        // Create two OTPs for the same user and scope
        $otp1 = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);
        Date::setTestNow(Date::now()->addMinute());
        $otp2 = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        // Try to verify with the older OTP (both are valid)
        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => $otp1->otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.message', 'Email verified successfully.');

        // User should be verified
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'email_verified_at' => Date::now(),
        ]);
    }

    /** @test */
    public function it_responds_with_validation_error_when_otp_has_less_than_6_digits(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'digits5@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => '12345', // Only 5 digits
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('data.errors.otp.0', 'The otp must be 6 digits.');
    }

    /** @test */
    public function it_responds_with_validation_error_when_otp_has_more_than_6_digits(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'digits7@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => '1234567', // 7 digits
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('data.errors.otp.0', 'The otp must be 6 digits.');
    }

    /** @test */
    public function it_responds_with_validation_error_when_otp_is_not_numeric(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'nonnumeric@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => '12345a', // Contains letter
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('data.errors.otp.0', 'The otp must be a number.');
    }

    /** @test */
    public function it_responds_with_validation_error_when_otp_is_missing(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'nootp@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('data.errors.otp.0', 'The otp field is required.');
    }

    /** @test */
    public function it_responds_with_validation_error_when_email_is_empty_string(): void
    {
        $response = $this->post('/verify-email', [
            'email' => '',
            'otp' => '123456',
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('data.errors.email.0', 'The email field is required.');
    }

    /** @test */
    public function it_responds_with_validation_error_when_otp_is_empty_string(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => 'emptyotp@gmail.com',
            'password' => Crypto::encryptString('password'),
        ]);

        $response = $this->post('/verify-email', [
            'email' => $user->email,
            'otp' => '',
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('data.errors.otp.0', 'The otp field is required.');
    }
}
