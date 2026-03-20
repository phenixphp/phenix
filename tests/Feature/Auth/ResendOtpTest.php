<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Mail\SendEmailVerificationOtp;
use App\Models\User;
use App\Models\UserOtp;
use Phenix\Facades\Crypto;
use Phenix\Facades\Mail;
use Phenix\Http\Constants\HttpStatus;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class ResendOtpTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_resend_otp_for_unverified_email(): void
    {
        Date::setTestNow(Date::now());
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        $response = $this->post('/resend-verification-otp', [
            'email' => $user->email,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', trans('auth.otp.email_verification.resent'));

        $this->assertDatabaseHas('user_one_time_passwords', [
            'id' => $otp->id,
            'user_id' => $user->id,
            'scope' => OneTimePasswordScope::VERIFY_EMAIL->value,
            'used_at' => null,
        ]);

        $this->assertEquals(
            2,
            UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::VERIFY_EMAIL->value)
                ->count()
        );

        Mail::expect(SendEmailVerificationOtp::class)->toBeSentTimes(1);
    }

    /** @test */
    public function it_does_not_resend_otp_when_email_is_already_verified(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
            'email_verified_at' => Date::now(),
        ]);

        $response = $this->post('/resend-verification-otp', [
            'email' => $user->email,
        ]);

        $response->assertUnprocessableEntity()
            ->assertJsonPath('errors.email.0', trans('validation.exists', ['field' => 'email']));

        Mail::expect(SendEmailVerificationOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_responds_unauthorized_when_authorization_token_is_present(): void
    {
        Mail::fake();

        $response = $this->post(
            '/resend-verification-otp',
            ['email' => $this->faker()->freeEmail()],
            [],
            ['Authorization' => 'Bearer any-token']
        );

        $response->assertUnauthorized()
            ->assertJsonPath('message', trans('auth.unauthorized'));

        Mail::expect(SendEmailVerificationOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_responds_too_many_requests_when_exceed_otp_limit(): void
    {
        Date::setTestNow(Date::now());
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Crypto::encryptString('password'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $user->createOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);
        }

        $response = $this->post('/resend-verification-otp', [
            'email' => $user->email,
        ]);

        $response->assertStatusCode(HttpStatus::TOO_MANY_REQUESTS)
            ->assertJsonPath('message', trans('auth.otp.limit_exceeded'));

        Mail::expect(SendEmailVerificationOtp::class)->toNotBeSent();
    }
}
