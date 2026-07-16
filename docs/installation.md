# Installation

SteelAnts Laravel-Auth is installed using Composer.


## Requirements

- Laravel 11 or 12
- PHP version compatible with your Laravel installation


## Install the Package

Install the package using Composer:

```bash
composer require steelants/laravel-auth
```

Laravel automatically discovers the service provider.

If package discovery is disabled, register the provider manually in `bootstrap/providers.php`:

```php
return [
    // ...
    SteelAnts\LaravelAuth\AuthServiceProvider::class,
];
```


## Run the Installer

Publish the authentication scaffolding:

```bash
php artisan install:auth
```

The installer:

1. Publishes `app/Http/Controllers/AuthController.php`.
2. Publishes the authentication views to `resources/views/auth`.
3. Adds `Route::auth();` to `routes/web.php`.

Existing files are only replaced after confirmation.

You can overwrite existing files without confirmation:

```bash
php artisan install:auth --force
```


## Published Views

The installer publishes the following views:

- `auth/login.blade.php`
- `auth/registration.blade.php`
- `auth/reset.blade.php`
- `auth/totp.blade.php`
- `auth/verify.blade.php`


## Migrations

Package migrations are loaded automatically.

The migrations add TOTP columns to the `users` table:

- `totp_secret`
- `totp_confirmed_at`
- `totp_force`

Run the migrations:

```bash
php artisan migrate
```

You can publish the migrations into your application:

```bash
php artisan vendor:publish --tag=auth-migrations
```

The migrations are only required when using the [Two-Factor Authentication](totp.md) feature.


## Configuration Files

Currently the package does not require additional configuration files.

All settings are handled using route options and controller traits.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
