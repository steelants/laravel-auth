<?php

namespace SteelAnts\LaravelAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use SteelAnts\LaravelAuth\Routing\AuthRoutesMixin;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Route::mixin(new AuthRoutesMixin);
    }
}