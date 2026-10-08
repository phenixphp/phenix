<?php

declare(strict_types=1);

namespace Tests\Unit\Tasks;

use App\Models\User;
use App\Tasks\DeleteExpiredPersonalAccessTokens;
use Phenix\Facades\Hash;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class DeleteExpiredPersonalAccessTokensTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function tearDown(): void
    {
        Date::setTestNow();

        parent::tearDown();
    }

    /** @test */
    public function it_deletes_tokens_that_are_already_expired(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $expiredToken = $user->createToken('expired-token', ['*'], Date::now()->subMinute());

        $result = $this->runTask(new DeleteExpiredPersonalAccessTokens());

        $this->assertTrue($result->isSuccess());
        $this->assertTrue($result->output());
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $expiredToken->id()]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /** @test */
    public function it_keeps_tokens_that_expire_in_the_future(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $activeToken = $user->createToken('active-token', ['*'], Date::now()->addMinutes(30));

        $result = $this->runTask(new DeleteExpiredPersonalAccessTokens());

        $this->assertTrue($result->isSuccess());
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $activeToken->id()]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /** @test */
    public function it_deletes_tokens_that_expire_exactly_at_the_current_time(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $token = $user->createToken('expiring-now-token', ['*'], Date::now());

        $result = $this->runTask(new DeleteExpiredPersonalAccessTokens());

        $this->assertTrue($result->isSuccess());
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->id()]);
    }

    /** @test */
    public function it_only_deletes_the_expired_tokens(): void
    {
        Date::setTestNow(Date::now());

        $user = $this->createUser();
        $expiredToken = $user->createToken('expired-token', ['*'], Date::now()->subMinutes(45));
        $activeToken = $user->createToken('active-token', ['*'], Date::now()->addMinutes(15));

        $result = $this->runTask(new DeleteExpiredPersonalAccessTokens());

        $this->assertTrue($result->isSuccess());
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $expiredToken->id()]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $activeToken->id()]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /** @test */
    public function it_reports_success_when_there_are_no_tokens_to_delete(): void
    {
        $result = $this->runTask(new DeleteExpiredPersonalAccessTokens());

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
}
