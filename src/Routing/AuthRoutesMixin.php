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
                    $this->get('login', 'AuthController@login')->name('login');
                    $this->post('login', 'AuthController@loginPost')->name('login.submit');
                }

                if ($options['logout'] ?? true) {
                    $this->post('logout', 'AuthController@logout')->name('logout');
                    $this->get('logout', 'AuthController@logout');
                }

                if ($options['register'] ?? true) {
                    $this->get('register', 'AuthController@register')->name('register');
                    $this->post('register', 'AuthController@registerPost')->name('register.submit');
                }

                if ($options['reset'] ?? true) {
                    $this->get('reset', 'AuthController@reset')->name('reset');
                    $this->post('reset', 'AuthController@resetPost')->name('reset.submit');
                    $this->get('password/reset/{token}', 'AuthController@resetPost')->name('password.reset');
                    $this->post('password/reset/', 'AuthController@resetPost')->name('password.reset.submit');
                }
            });
        };
    }
}
