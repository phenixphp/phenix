<?php

declare(strict_types=1);

namespace Tests\Unit\Tasks;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use App\Models\UserOtp;
use App\Tasks\DeleteExpiredUserOtps;
use Phenix\Facades\Hash;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class DeleteExpiredUserOtpsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function tearDown(): void
    {
        Date::setTestNow();

        parent::tearDown();
    }

    /** @test */
    public function it_deletes_expired_otps_that_were_not_used(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $expiredOtp = $this->createUserOtp($user, expiresAt: Date::now()->subMinute());

        $result = $this->runTask(new DeleteExpiredUserOtps());

        $this->assertTrue($result->isSuccess());
        $this->assertTrue($result->output());
        $this->assertDatabaseMissing('user_one_time_passwords', ['id' => $expiredOtp->id]);
        $this->assertDatabaseCount('user_one_time_passwords', 0);
    }

    /** @test */
    public function it_keeps_otps_that_are_not_expired_yet(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $activeOtp = $this->createUserOtp($user, expiresAt: Date::now()->addMinute());

        $result = $this->runTask(new DeleteExpiredUserOtps());

        $this->assertTrue($result->isSuccess());
        $this->assertDatabaseHas('user_one_time_passwords', ['id' => $activeOtp->id]);
        $this->assertDatabaseCount('user_one_time_passwords', 1);
    }

    /** @test */
    public function it_keeps_otps_that_expire_exactly_at_the_current_time(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $otp = $this->createUserOtp($user, expiresAt: Date::now());

        $result = $this->runTask(new DeleteExpiredUserOtps());

        $this->assertTrue($result->isSuccess());
        $this->assertDatabaseHas('user_one_time_passwords', ['id' => $otp->id]);
    }

    /** @test */
    public function it_keeps_used_otps_even_when_they_are_expired(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $usedOtp = $this->createUserOtp(
            $user,
            expiresAt: Date::now()->subMinute(),
            usedAt: Date::now()->subMinutes(2),
        );

        $result = $this->runTask(new DeleteExpiredUserOtps());

        $this->assertTrue($result->isSuccess());
        $this->assertDatabaseHas('user_one_time_passwords', ['id' => $usedOtp->id]);
    }

    /** @test */
    public function it_only_deletes_the_expired_and_unused_otps(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $expiredOtp = $this->createUserOtp($user, expiresAt: Date::now()->subMinutes(30));
        $activeOtp = $this->createUserOtp($user, expiresAt: Date::now()->addMinutes(9));
        $usedOtp = $this->createUserOtp(
            $user,
            expiresAt: Date::now()->subMinutes(15),
            usedAt: Date::now()->subMinutes(20),
        );

        $result = $this->runTask(new DeleteExpiredUserOtps());

        $this->assertTrue($result->isSuccess());
        $this->assertDatabaseMissing('user_one_time_passwords', ['id' => $expiredOtp->id]);
        $this->assertDatabaseHas('user_one_time_passwords', ['id' => $activeOtp->id]);
        $this->assertDatabaseHas('user_one_time_passwords', ['id' => $usedOtp->id]);
        $this->assertDatabaseCount('user_one_time_passwords', 2);
    }

    /** @test */
    public function it_reports_success_when_there_are_no_otps_to_delete(): void
    {
        $result = $this->runTask(new DeleteExpiredUserOtps());

        $this->assertTrue($result->isSuccess());
        $this->assertTrue($result->output());
        $this->assertNull($result->message());
    }

    private function createUser(): User
    {
        return User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);
    }

    private function createUserOtp(User $user, Date $expiresAt, Date|null $usedAt = null): UserOtp
    {
        $userOtp = UserOtp::fromScope(OneTimePasswordScope::VERIFY_EMAIL);
        $userOtp->userId = $user->id;
        $userOtp->expiresAt = $expiresAt;
        $userOtp->usedAt = $usedAt;
        $userOtp->save();

        return $userOtp;
    }
}
