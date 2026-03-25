<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Phenix\Facades\Hash;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class TokenRefreshTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_refreshes_current_token_and_expires_the_previous_one(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $oldToken = $user->createToken('auth_token');

        $response = $this->post(
            path: '/token/refresh',
            headers: ['Authorization' => 'Bearer ' . $oldToken->toString()]
        );

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer');

        $data = $response->getDecodedBody();

        $this->assertNotEmpty($data['access_token'] ?? null);
        $this->assertNotEmpty($data['expires_at'] ?? null);
        $this->assertNotSame($oldToken->toString(), $data['access_token']);

        $this->assertDatabaseHas('personal_access_tokens', [
            'token' => hash('sha256', $data['access_token']),
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $oldToken->id(),
            'expires_at' => Date::now()->toDateTimeString(),
        ]);
    }

    /** @test */
    public function it_cannot_use_old_token_after_refresh(): void
    {
        Date::setTestNow(Date::now());

        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $oldToken = $user->createToken('auth_token');

        $this->post(
            path: '/token/refresh',
            headers: ['Authorization' => 'Bearer ' . $oldToken->toString()]
        )->assertOk();

        Date::setTestNow(Date::now()->addSecond());

        $this->post(
            path: '/logout',
            headers: ['Authorization' => 'Bearer ' . $oldToken->toString()]
        )->assertUnauthorized();
    }

    /** @test */
    public function it_responds_unauthorized_without_token(): void
    {
        $this->post('/token/refresh')
            ->assertUnauthorized();
    }
}
