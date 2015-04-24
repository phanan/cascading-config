# Cascading Config 

[![Build Status](https://travis-ci.org/phanan/cascading-config.svg?branch=master)](https://travis-ci.org/phanan/cascading-config)
[![Dependency Status](https://gemnasium.com/phanan/cascading-config.svg)](https://gemnasium.com/phanan/cascading-config)
[![License](https://poser.pugx.org/phanan/cascading-config/license.svg)](https://packagist.org/packages/phanan/cascading-config)

A simple package that brings the cascading configuration system back into Laravel 5.

## Requirements

* Laravel 5, duh!

## Features
* Laravel-4 style cascading config (can't believe I'm writing this)
* [Nested configuration](https://github.com/laravel/framework/commit/fee982004a795058ab6a66e1600c11aac6748acf) is fully supported

## Installation

First, require `phanan/cascading-config` into your `composer.json` and run `composer update`.

``` 
    "require": {
        "laravel/framework": "5.0.*",
        "phanan/cascading-config": "~1.0"
    },
```

After the package is downloaded, open `config/app.php` and add its service provider class:

``` php
    'providers' => [

        // ...
        'App\Providers\ConfigServiceProvider',
        'App\Providers\EventServiceProvider',
        'App\Providers\RouteServiceProvider',

        'PhanAn\CascadingConfig\CascadingConfigServiceProvider',

    ],
```

An environment-based configuration directory should have a name with this format `config.{APP_ENV}`, and live in the same directory with the default `config` dir. For a start, let's create the directory for your `local` environment:

``` bash
php artisan vendor:publish
```

Your working directory now should have something like this:

```
config
├── app.php
├── auth.php
├── cache.php
├── compile.php
├── database.php
└── ...
config.local
├── app.php
└── nested
    └── app.php
```

## Usage

1. Fill the configuration into your environment-based config directory (`config.local`, `config.staging`, `config.production`), just like what you've always done in Laravel 4
1. Call `config($key)` just like what you've always done in Laravel 5

## Notes

Because of the way `array_merge_recursive()` works, a config key with value being an indexed (non-associative) array (for instance, `app.providers`) will have the value's items overridden. See #6 if for more details on this behavior, and how to work around it.

## License

MIT © [Phan An](http://phanan.net)
