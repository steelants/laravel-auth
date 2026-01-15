<?php

namespace SteelAnts\LaravelAuth\Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Support\Facades\Route;
use SteelAnts\LaravelBoilerplate\Support\MenuItemLink;
use Tests\TestCase;

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
