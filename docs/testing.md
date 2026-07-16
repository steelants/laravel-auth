# Testing

SteelAnts Laravel-Auth uses automated tests to verify package functionality.

The package uses:

- PHPUnit
- Orchestra Testbench


## Install Dependencies

Install development dependencies:

```bash
composer install
```


## Running Tests

Run the complete test suite:

```bash
./vendor/bin/phpunit
```


## Running Specific Tests

You can run only selected tests by providing the test file:

```bash
./vendor/bin/phpunit tests/Feature/AuthenticationTest.php
```


## Adding New Tests

When adding new functionality:

1. Create a test covering the new behavior.
2. Run the complete test suite.
3. Verify existing functionality is not affected.

Example test structure:

```
tests/
`-- Feature/
    |-- AuthenticationTest.php
    `-- ...
```


## Before Creating a Pull Request

Before submitting changes:

Run:

```bash
composer install
```

Then:

```bash
./vendor/bin/phpunit
```

Check the code style:

```bash
composer lint
```

All tests should pass before merging changes.


## Next Steps

Continue with:

- [Development](development.md)
- [Usage](usage.md)
- [Configuration](configuration.md)
