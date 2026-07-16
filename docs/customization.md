# Customization

SteelAnts Laravel-Auth can be customized using optional hook methods in your `AuthController`.

The `Authentication` trait detects the hooks automatically.


## Available Hooks

| Method | Description |
|---|---|
| `verifyLoginAttempt(Request $request): bool` | Rejects a login attempt before authentication |
| `loginAttempt(array $credentials, bool $remember): bool` | Replaces the default authentication logic |
| `verifyResetAttempt(Request $request): void` | Runs before sending a password reset link |
| `redirectTo(): string` | Overrides the default redirect path |


## Rejecting Login Attempts

Use `verifyLoginAttempt()` to block suspicious login attempts:

```php
public function verifyLoginAttempt(Request $request): bool
{
    return RateLimiter::tooManyAttempts($request->ip(), 5);
}
```

Returning `true` rejects the login attempt.


## Custom Authentication Logic

Use `loginAttempt()` to replace the default `Auth::attempt()` call:

```php
public function loginAttempt(array $credentials, bool $remember): bool
{
    return $this->ldap->authenticate($credentials, $remember);
}
```

This is useful for LDAP or external authentication providers.


## Custom Redirect Path

By default users are redirected to the `home` route.

Override the redirect path:

```php
public function redirectTo(): string
{
    return route('dashboard');
}
```


## Password Reset Validation

Use `verifyResetAttempt()` to validate reset requests:

```php
public function verifyResetAttempt(Request $request): void
{
    // custom throttling or validation
}
```


## Customizing Views

The installer publishes all views to your application:

```
resources/views/auth/
├── login.blade.php
├── registration.blade.php
├── reset.blade.php
├── totp.blade.php
└── verify.blade.php
```

Published views can be modified freely.

Re-publish the original views:

```bash
php artisan install:auth --force
```

> The `--force` flag overwrites your changes.
> Make sure your modifications are committed first.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
- [Development](development.md)
