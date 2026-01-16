<?php

namespace SteelAnts\LaravelAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use SteelAnts\LaravelAuth\Middleware\EnsureEmailIsVerified;
use SteelAnts\LaravelAuth\Middleware\EnsureTotpVerified;
use SteelAnts\LaravelAuth\Routing\AuthRoutesMixin;
use SteelAnts\LaravelAuth\Console\Commands\InstallCommand;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([InstallCommand::class]);
		$this->loadMigrationsFrom(dirname(__DIR__) . '/database/migrations');
    }

    public function boot()
    {
        $this->app['router']->aliasMiddleware('verified', EnsureEmailIsVerified::class);
        $this->app['router']->aliasMiddleware('verified.totp', EnsureTotpVerified::class);

        //Do not register routes before instalation since it can collide vith otehr modules
        if (class_exists('App\Http\Controllers\AuthController')) {
			Route::mixin(new AuthRoutesMixin());
        }

    	if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'auth-migrations');

    }
}
