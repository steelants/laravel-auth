<?php

namespace Laravel\Ui\Tests\AuthBackend;

use Illuminate\Auth\Events\Attempting;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function it_can_user_login()
    {
        $user = UserFactory::new()->create();

        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press('login')
            ->seePageIs('/home');
    }
}