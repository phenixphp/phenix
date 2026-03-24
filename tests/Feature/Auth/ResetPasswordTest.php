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

class ResetPasswordTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_resets_password_marks_otp_as_used_and_revokes_all_tokens(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('OldP@ssw0rd1'),
            'email_verified_at' => Date::now(),
        ]);

        $firstToken = $user->createToken('first-token');
        $secondToken = $user->createToken('second-token');
        $otp = $user->createOneTimePassword(OneTimePasswordScope::RESET_PASSWORD);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => $otp->otp,
            'password' => 'N3wP@ssw0rd1',
            'password_confirmation' => 'N3wP@ssw0rd1',
        ])->assertOk()
            ->assertJsonPath('message', trans('auth.password_reset.reset'));
    
        $updatedUser = User::find($user->id);

        $this->assertNotNull($updatedUser);
        $this->assertTrue(Hash::verify($updatedUser->password, 'N3wP@ssw0rd1'));

        $this->assertDatabaseHas('user_one_time_passwords', [
            'id' => $otp->id,
            'used_at' => Date::now(),
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $firstToken->id(),
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $secondToken->id(),
        ]);
    }

    /** @test */
    public function it_responds_not_found_for_non_existing_otp(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('OldP@ssw0rd1'),
            'email_verified_at' => Date::now(),
        ]);

        $token = $user->createToken('active-token');

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'N3wP@ssw0rd1',
            'password_confirmation' => 'N3wP@ssw0rd1',
        ])->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));

        $updatedUser = User::find($user->id);

        $this->assertNotNull($updatedUser);
        $this->assertTrue(Hash::verify($updatedUser->password, 'OldP@ssw0rd1'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->id(),
        ]);
    }

    /** @test */
    public function it_responds_not_found_when_otp_has_different_scope(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('OldP@ssw0rd1'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::LOGIN);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => $otp->otp,
            'password' => 'N3wP@ssw0rd1',
            'password_confirmation' => 'N3wP@ssw0rd1',
        ])->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_responds_not_found_when_otp_is_already_used(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('OldP@ssw0rd1'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::RESET_PASSWORD);
        $otp->usedAt = Date::now();
        $otp->save();

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => $otp->otp,
            'password' => 'N3wP@ssw0rd1',
            'password_confirmation' => 'N3wP@ssw0rd1',
        ])->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_responds_not_found_when_otp_is_expired(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('OldP@ssw0rd1'),
            'email_verified_at' => Date::now(),
        ]);

        $otp = $user->createOneTimePassword(OneTimePasswordScope::RESET_PASSWORD);

        Date::setTestNow(Date::now()->addMinutes(11));

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => $otp->otp,
            'password' => 'N3wP@ssw0rd1',
            'password_confirmation' => 'N3wP@ssw0rd1',
        ])->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_responds_not_found_when_email_is_not_verified(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('OldP@ssw0rd1'),
        ]);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'N3wP@ssw0rd1',
            'password_confirmation' => 'N3wP@ssw0rd1',
        ])->assertNotFound()
            ->assertJsonPath('message', trans('auth.otp.invalid'));
    }

    /** @test */
    public function it_validates_password_payload_for_reset_password(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('OldP@ssw0rd1'),
            'email_verified_at' => Date::now(),
        ]);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'N3wP@ssw0rd1',
            'password_confirmation' => 'DifferentValue1',
        ])->assertUnprocessableEntity();
    }
}
