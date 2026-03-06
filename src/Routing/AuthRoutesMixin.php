<?php

namespace SteelAnts\LaravelAuth\Routing;

class AuthRoutesMixin
{
    public function auth()
    {
        return function ($options = []) {
            $namespace = 'App\Http\Controllers';
            $this->group(['namespace' => $namespace], function () use ($options) {
                if ($options['login'] ?? true) {
                    $this->get('/login', 'AuthController@login')->name('login');
                    $this->post('/login', 'AuthController@loginPost')->name('login.submit');
                }

                if ($options['logout'] ?? true) {
					$this->post('/logout', 'AuthController@logout')->withoutMiddleware(['verified', 'verified.totp'])->name('logout');
                    $this->get('/logout', 'AuthController@logout')->withoutMiddleware(['verified', 'verified.totp']);
                }

                if ($options['register'] ?? true) {
                    $this->get('/register', 'AuthController@register')->name('register');
                    $this->post('/register', 'AuthController@registerPost')->name('register.submit');
                }

                if ($options['reset'] ?? true) {
                    $this->get('/password/reset', 'AuthController@reset')->name('password');
                    $this->post('/password/email', 'AuthController@resetPost')->name('password.email');
                    $this->get('/password/reset/{token}', 'AuthController@resetToken')->middleware(['throttle:6,1'])->name('password.reset');
                    $this->post('/password/reset/', 'AuthController@resetPasswordSubmit')->middleware(['throttle:6,1'])->name('password.update');
                }

				if ($options['verify'] ?? false) {
					$this->pushMiddlewareToGroup('web', 'verified');
					$this->get('/email/verify', 'AuthController@verificationNotice')->middleware('auth') ->withoutMiddleware('verified') ->name('verification.notice');
					$this->get('/email/verify/{id}/{hash}', 'AuthController@verifyEmail')->middleware(['auth', 'signed', 'throttle:6,1']) ->withoutMiddleware('verified')->name('verification.verify');
					$this->post('/email/resend', 'AuthController@resendVerification')->middleware(['auth', 'throttle:6,1']) ->withoutMiddleware('verified')->name('verification.resend');
                }

                if ($options['totp'] ?? false) {
					$this->pushMiddlewareToGroup('web', 'verified.totp');
					$this->get('/two-factor', 'AuthController@totpPrompt') ->middleware('auth') ->withoutMiddleware('verified.totp') ->name('totp.prompt');
					$this->post('/two-factor', 'AuthController@totpVerify') ->middleware(['auth', 'throttle:10,1']) ->withoutMiddleware('verified.totp') ->name('totp.verify');
                }
            });
        };
    }
}
