<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Constants\OneTimePasswordScope;
use App\Mail\SendResetPasswordOtp;
use App\Models\User;
use App\Models\UserOtp;
use Phenix\Facades\Hash;
use Phenix\Facades\Mail;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_sends_a_reset_password_otp_for_verified_users(): void
    {
        Date::setTestNow(Date::now());
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertOk()
            ->assertJsonPath('message', trans('auth.password_reset.sent'));

        $this->assertDatabaseHas('user_one_time_passwords', [
            'user_id' => $user->id,
            'scope' => OneTimePasswordScope::RESET_PASSWORD->value,
        ]);

        Mail::expect(SendResetPasswordOtp::class)->toBeSentTimes(1);
    }

    /** @test */
    public function it_returns_a_generic_response_for_non_existing_emails(): void
    {
        Mail::fake();

        $this->post('/forgot-password', [
            'email' => $this->faker()->freeEmail(),
        ])->assertOk()
            ->assertJsonPath('message', trans('auth.password_reset.sent'));

        $this->assertSame(
            0,
            UserOtp::query()
                ->whereEqual('scope', OneTimePasswordScope::RESET_PASSWORD->value)
                ->count()
        );

        Mail::expect(SendResetPasswordOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_returns_a_generic_response_for_unverified_users(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
        ]);

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertOk()
            ->assertJsonPath('message', trans('auth.password_reset.sent'));

        $this->assertSame(
            0,
            UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::RESET_PASSWORD->value)
                ->count()
        );

        Mail::expect(SendResetPasswordOtp::class)->toNotBeSent();
    }

    /** @test */
    public function it_returns_a_generic_response_when_the_reset_otp_limit_is_exceeded(): void
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
            $user->createOneTimePassword(OneTimePasswordScope::RESET_PASSWORD);
        }

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertOk()
            ->assertJsonPath('message', trans('auth.password_reset.sent'));

        $this->assertSame(
            5,
            UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::RESET_PASSWORD->value)
                ->count()
        );

        Mail::expect(SendResetPasswordOtp::class)->toNotBeSent();
    }
}
