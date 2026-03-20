<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Phenix\Facades\Hash;
use Phenix\Testing\Concerns\RefreshDatabase;
use Phenix\Testing\Concerns\WithFaker;
use Phenix\Util\Date;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function it_logs_out_and_revokes_only_the_current_token(): void
    {
        $user = User::create([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->freeEmail(),
            'password' => Hash::make('P@ssw0rd12'),
            'email_verified_at' => Date::now(),
        ]);

        $currentToken = $user->createToken('current-token');
        $otherToken = $user->createToken('other-token');

        $response = $this->post(
            path: '/logout',
            headers: ['Authorization' => 'Bearer ' . $currentToken->toString()]
        );

        $response->assertOk()
            ->assertJsonPath('message', trans('auth.logout.success'));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $currentToken->id(),
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $otherToken->id(),
        ]);
    }

    /** @test */
    public function it_responds_unauthorized_when_logging_out_without_a_token(): void
    {
        $this->post('/logout')
            ->assertUnauthorized()
            ->assertJsonPath('message', trans('auth.unauthorized'));
    }
}
