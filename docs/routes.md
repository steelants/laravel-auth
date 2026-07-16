# Routes

SteelAnts Laravel-Auth registers authentication routes using the `Route::auth()` mixin.

Routes are grouped by feature and can be enabled or disabled individually.

For the available options see:

[Configuration documentation](configuration.md)


## Login and Logout

Enabled by default.

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET | `/login` | `login` | - |
| POST | `/login` | `login.submit` | - |
| GET | `/logout` | - | - |
| POST | `/logout` | `logout` | - |

Logout routes are excluded from the email verification and TOTP middleware so users can always log out.


## Registration

Enabled by default.

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET | `/register` | `register` | - |
| POST | `/register` | `register.submit` | - |


## Password Reset

Enabled by default.

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET | `/password/reset` | `password` | - |
| POST | `/password/email` | `password.email` | - |
| GET | `/password/reset/{token}` | `password.reset` | `throttle:6,1` |
| POST | `/password/reset/` | `password.update` | `throttle:6,1` |


## Email Verification

Disabled by default.

Enable using:

```php
Route::auth(['verify' => true]);
```

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET | `/email/verify` | `verification.notice` | `auth` |
| GET | `/email/verify/{id}/{hash}` | `verification.verify` | `auth`, `signed`, `throttle:6,1` |
| POST | `/email/resend` | `verification.resend` | `auth`, `throttle:6,1` |

When enabled, the `EnsureEmailIsVerified` middleware is pushed to the `web` middleware group.


## Two-Factor Authentication

Disabled by default.

Enable using:

```php
Route::auth(['totp' => true]);
```

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET | `/two-factor` | `totp.prompt` | `auth` |
| POST | `/two-factor` | `totp.verify` | `auth`, `throttle:10,1` |

When enabled, the `EnsureTotpVerified` middleware is pushed to the `web` middleware group.


## Route Registration

Routes are only registered when `App\Http\Controllers\AuthController` exists.

This prevents collisions before the package is installed.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
- [Email Verification](email-verification.md)
- [Two-Factor Authentication](totp.md)
