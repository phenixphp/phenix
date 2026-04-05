<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Phenix\Facades\Hash;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class TokenManagementTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_lists_active_tokens_for_authenticated_user(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $tokenA = $user->createToken('token-a');
        $tokenB = $user->createToken('token-b');

        $expiredToken = $user->createToken('token-expired', ['*'], Date::now()->subMinute());

        $response = $this->get(
            path: route('tokens.index'),
            headers: ['Authorization' => 'Bearer ' . $tokenA->toString()]
        );

        $response->assertOk();

        $data = $response->getDecodedBody();

        $this->assertCount(2, $data);

        $ids = array_column($data, 'id');
        $this->assertContains($tokenA->id(), $ids);
        $this->assertContains($tokenB->id(), $ids);
        $this->assertNotContains($expiredToken->id(), $ids);
    }

    /** @test */
    public function it_revokes_a_specific_token_by_id(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $tokenA = $user->createToken('token-a');
        $tokenB = $user->createToken('token-b');

        $response = $this->delete(
            path: route('tokens.destroy', ['id' => $tokenA->id()]),
            headers: ['Authorization' => 'Bearer ' . $tokenB->toString()]
        );

        $response->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenA->id(),
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenB->id(),
        ]);
    }

    /** @test */
    public function it_responds_not_found_when_revoking_another_users_token(): void
    {
        $userA = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $userB = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $tokenA = $userA->createToken('token-a');
        $tokenB = $userB->createToken('token-b');

        $response = $this->delete(
            path: route('tokens.destroy', ['id' => $tokenB->id()]),
            headers: ['Authorization' => 'Bearer ' . $tokenA->toString()]
        );

        $response->assertNotFound()
            ->assertJsonPath('message', trans('auth.token.not_found'));
    }
}
