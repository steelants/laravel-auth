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
### Dev Enviroment
1) Clone Repo to `[LARVEL-ROOT]/packages/`
2) Modify ;composer.json`
```json
    "autoload": {
        "psr-4": {
            ...
            "SteelAnts\\LaravelAuth\\": "packages/laravel-auth/src/"
            ...
        }
    },
```
3) Add (code below) to: `[LARVEL-ROOT]/bootstrap/providers.php`
```php
SteelAnts\LaravelAuth\AuthServiceProvider::class,
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
