# Development

This guide describes how to develop SteelAnts Laravel-Auth locally inside a Laravel application.


## Local Setup

Create a packages directory and clone the repository:

```bash
mkdir packages
git clone https://github.com/steelants/laravel-auth.git ./packages/laravel-auth
```

Update the autoload section of your application `composer.json`:

```json
"autoload": {
    "psr-4": {
        "SteelAnts\\LaravelAuth\\": "packages/laravel-auth/src/"
    }
}
```

Refresh the autoloader:

```bash
composer dump-autoload
```

Register the service provider in `bootstrap/providers.php`:

```php
return [
    // ...
    SteelAnts\LaravelAuth\AuthServiceProvider::class,
];
```

Apply the package scaffolding:

```bash
php artisan install:auth --force
```


## Development Workflow

1. Create a feature branch.
2. Implement changes.
3. Add or update tests.
4. Run the test suite.
5. Merge changes into the development branch.

Before running the test suite see:

[Testing documentation](testing.md)


## Code Style

The package uses PHP_CodeSniffer with the Slevomat coding standard.

Check the code style:

```bash
composer lint
```

Fix the code style automatically:

```bash
composer format
```

Run static analysis:

```bash
composer check-static
```


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
- [Testing](testing.md)
