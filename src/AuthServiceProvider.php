<?php

namespace SteelAnts\LaravelAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use SteelAnts\LaravelAuth\Routing\AuthRoutesMixin;
use SteelAnts\LaravelAuth\Console\Commands\InstallCommand;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (!$this->app->runningInConsole())
            return;

        $this->commands([
            InstallCommand::class
        ]);
    }

    public function boot()
    {
        Route::mixin(new AuthRoutesMixin);
    }
}