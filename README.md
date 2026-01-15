# Laravel-Auth
## Currently WIP
### Created by: [SteelAnts s.r.o.](https://www.steelants.cz/)

[![Total Downloads](https://img.shields.io/packagist/dt/steelants/laravel-auth.svg?style=flat-square)](https://packagist.org/packages/steelants/laravel-auth)

## Install
1) Artisan Command
```bash
install:auth
```
2) add routes to __web.php__
```php
Route::auth();
```

### Optional Features
*Both features are disabled by default to avoid route collisions.

**Email verification**
- Protects routes with the `verified` middleware and provides notice/verify/resend endpoints when enabled.
- Requires `Route::auth(['verify' => true]);` and the `HandlesEmailVerification` plugin trait added to your `AuthController` next to `Authentication`.

**MFA (TOTP)**
- Adds TOTP setup/verification screens and enforces checks via the `verified.totp` middleware.
- Requires `Route::auth(['totp' => true]);` and the `HandlesTotp` plugin trait added to your `AuthController` next to `Authentication`.
- Install QR dependency in your app: `composer require endroid/qr-code`.

### Auth controller setup
Your `AuthController` pulls in the core auth trait, plus the plugin traits for optional features:
```php
use SteelAnts\LaravelAuth\Traits\Authentication;
use SteelAnts\LaravelAuth\Traits\HandlesEmailVerification;
use SteelAnts\LaravelAuth\Traits\HandlesTotp;

class AuthController extends Controller
{
    use Authentication;
    use HandlesEmailVerification; // enable email verification flows
    use HandlesTotp;              // enable TOTP MFA flows
}
```

## Development

1. Create subfolder `/packages` at root of your laravel project

2. clone repository to sub folder `/packages` (you need to be positioned at root of your laravel project in your terminal)
```bash
git clone https://github.com/steelants/laravel-auth.git ./packages/laravel-auth
```

3. edit composer.json file
```json
"autoload": {
	"psr-4": {
		"SteelAnts\\LaravelAuth\\": "packages/laravel-auth/src/"
	}
}
```

4. Add provider to `bootstrap/providers.php`
```php
return [
	...
	SteelAnts\LaravelAuth\AuthServiceProvider::class,
	...
];
```

6. aplicate packages changes
```bash
php artisan install:auth --force
```

## Contributors
<a href="https://github.com/steelants/laravel-auth/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=steelants/laravel-auth" />
</a>

## Other Packages
[steelants/datatable](https://github.com/steelants/Livewire-DataTable)

[steelants/form](https://github.com/steelants/Laravel-Form)

[steelants/modal](https://github.com/steelants/Livewire-Modal)

[steelants/boilerplate](https://github.com/steelants/Laravel-Boilerplate)
