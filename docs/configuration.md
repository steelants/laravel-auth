# Configuration

SteelAnts Laravel-Auth is configured directly using route options and controller traits.

The package does not require additional configuration files.


## Route Options

Authentication features are enabled using options passed to `Route::auth()`:

```php
Route::auth([
    'register' => false,
    'verify' => true,
]);
```

Available options:

| Option | Default | Description |
|---|---|---|
| `login` | `true` | Login form and submit routes |
| `logout` | `true` | Logout routes |
| `register` | `true` | Registration routes |
| `reset` | `true` | Password reset routes |
| `verify` | `false` | Email verification routes |
| `totp` | `false` | TOTP two-factor routes |

Optional features are disabled by default to avoid route collisions.


## Middleware

The package registers two middleware aliases:

| Alias | Middleware | Description |
|---|---|---|
| `verified` | `EnsureEmailIsVerified` | Requires a verified e-mail address |
| `verified.totp` | `EnsureTotpVerified` | Requires a passed TOTP check |

When the `verify` or `totp` option is enabled, the corresponding middleware is automatically pushed to the `web` middleware group.


## Email Verification

Enable email verification routes:

```php
Route::auth(['verify' => true]);
```

Add the plugin trait to your `AuthController`:

```php
use HandlesEmailVerification;
```

For more information see:

[Email Verification documentation](email-verification.md)


## Two-Factor Authentication

Enable TOTP routes:

```php
Route::auth(['totp' => true]);
```

Add the plugin trait to your `AuthController`:

```php
use HandlesTotpVerification;
```

For more information see:

[Two-Factor Authentication documentation](totp.md)


## Complete Example

Example route registration:

```php
Route::auth([
    'register' => false,
    'verify' => true,
    'totp' => true,
]);
```

Example controller:

```php
namespace App\Http\Controllers;

use SteelAnts\LaravelAuth\Traits\Authentication;
use SteelAnts\LaravelAuth\Traits\HandlesEmailVerification;
use SteelAnts\LaravelAuth\Traits\HandlesTotpVerification;

class AuthController extends Controller
{
    use Authentication;
    use HandlesEmailVerification;
    use HandlesTotpVerification;
}
```


## Next Steps

Continue with:

- [Usage](usage.md)
- [Routes](routes.md)
- [Email Verification](email-verification.md)
- [Two-Factor Authentication](totp.md)
- [Customization](customization.md)
