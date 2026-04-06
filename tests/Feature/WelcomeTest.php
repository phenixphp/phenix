<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class WelcomeTest extends TestCase
{
    /** @test */
    public function it_responses_successfully(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertBodyContains('Hello, world!');
    }
}
