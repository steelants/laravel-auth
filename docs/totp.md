# Two-Factor Authentication (TOTP)

SteelAnts Laravel-Auth provides optional TOTP based two-factor authentication.

The feature is disabled by default to avoid route collisions.

TOTP codes are compatible with standard authenticator apps (Google Authenticator, Microsoft Authenticator, Authy and others).


## Enable TOTP

Enable the TOTP routes:

```php
Route::auth(['totp' => true]);
```

Add the plugin trait to your `AuthController` next to `Authentication`:

```php
use SteelAnts\LaravelAuth\Traits\Authentication;
use SteelAnts\LaravelAuth\Traits\HandlesTotpVerification;

class AuthController extends Controller
{
    use Authentication;
    use HandlesTotpVerification;
}
```


## Database

The feature requires additional columns on the `users` table.

The package migration adds:

| Column | Type | Description |
|---|---|---|
| `totp_secret` | string, nullable | The confirmed TOTP secret |
| `totp_confirmed_at` | timestamp, nullable | When the secret was confirmed |
| `totp_force` | boolean, default `false` | Requires TOTP setup on next login |

Run the migrations:

```bash
php artisan migrate
```


## How It Works

**Setup**

When a user without a TOTP secret opens `/two-factor`, a new secret is generated and displayed as a QR code.

The user scans the QR code with an authenticator app and confirms the setup by entering a 6-digit code.

After confirmation the secret is stored on the user.

**Verification**

Users with a stored secret must enter a valid 6-digit code after login.

A passed check is stored in the session as `totp_passed`.


## Forcing TOTP Setup

You can require TOTP setup for individual users:

```php
$user->totp_force = true;
```

Users with `totp_force` enabled are redirected to the TOTP setup until a secret is confirmed.


## Middleware

The package registers the `verified.totp` middleware alias.

When the feature is enabled, the middleware is automatically pushed to the `web` middleware group.

Users requiring a TOTP check are redirected to `totp.prompt`.

JSON requests receive a `403` response instead of a redirect.

> The middleware throws a `LogicException` when the TOTP routes are not enabled.
> Always enable the feature using `Route::auth(['totp' => true])`.


## QR Codes

QR codes are generated using the `endroid/qr-code` package.

The dependency is installed automatically with this package.


## Views

The TOTP setup and verification screens are rendered using the published view:

```
resources/views/auth/totp.blade.php
```

You can modify the view freely.


## Complete Example

Example setup:

```php
// routes/web.php
Route::auth(['totp' => true]);
```

```php
// app/Http/Controllers/AuthController.php
class AuthController extends Controller
{
    use Authentication;
    use HandlesTotpVerification;
}
```


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
- [Email Verification](email-verification.md)
- [Customization](customization.md)
