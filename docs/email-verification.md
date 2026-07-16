# Email Verification

SteelAnts Laravel-Auth provides optional e-mail verification support.

The feature is disabled by default to avoid route collisions.


## Enable Email Verification

Enable the verification routes:

```php
Route::auth(['verify' => true]);
```

Add the plugin trait to your `AuthController` next to `Authentication`:

```php
use SteelAnts\LaravelAuth\Traits\Authentication;
use SteelAnts\LaravelAuth\Traits\HandlesEmailVerification;

class AuthController extends Controller
{
    use Authentication;
    use HandlesEmailVerification;
}
```


## User Model

Your `User` model must implement the `MustVerifyEmail` contract:

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    // ...
}
```


## Routes

The feature provides the following endpoints:

| Route | Description |
|---|---|
| `verification.notice` | Shows the verification notice page |
| `verification.verify` | Verifies the e-mail using a signed link |
| `verification.resend` | Resends the verification e-mail |

For details see:

[Routes documentation](routes.md)


## Middleware

The package registers the `verified` middleware alias.

When the feature is enabled, the middleware is automatically pushed to the `web` middleware group.

Unverified users are redirected to `verification.notice`.

JSON requests receive a `403` response instead of a redirect.

You can also protect individual routes:

```php
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified']);
```


## Views

The verification notice is rendered using the published view:

```
resources/views/auth/verify.blade.php
```

You can modify the view freely.


## Complete Example

Example setup:

```php
// routes/web.php
Route::auth(['verify' => true]);
```

```php
// app/Http/Controllers/AuthController.php
class AuthController extends Controller
{
    use Authentication;
    use HandlesEmailVerification;
}
```


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
- [Two-Factor Authentication](totp.md)
- [Customization](customization.md)
