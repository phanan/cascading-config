# Remote

*Remote* is a simple package that brings the ability to do remote connections back into Laravel 5. This package makes use of the awesome [phpseclib](https://github.com/phpseclib/phpseclib) behind the scene.

## Requirements

* PHP >= 5.4
* Anything required by phpseclib

## Installation

First, require `phanan/remote` into your `composer.json` and run `composer update`.

``` 
    "require": {
        "laravel/framework": "5.0.*",
        "phanan/remote": "dev-master"
    },
```

After the package is downloaded, open `config/app.php` and add its service provider class:

``` php
    'providers' => [

        // ...
        'App\Providers\ConfigServiceProvider',
        'App\Providers\EventServiceProvider',
        'App\Providers\RouteServiceProvider',

        'PhanAn\Remote\RemoteServiceProvider',

    ],
```

Now you need a sample configuration file:

``` bash
php artisan vendor:publish
```

Look for a `remote.php` file under your `config` directory and modify it to fit your needs.


## Usage

Using `Remote` is very simple: Just initialize a `PhanAn\Remote\Remote` object, says `$connection`. You don't even need to specify an argument -- `Remote` will pick the default configuration for you, and log you in.


Here's where the magic happens. Literally, `Remote` makes use of the magic function `__call()` to pass all unrecognized methods to the `phpseclib\Net\SFTP` object underneath. Which means, you can call any `phpseclib\Net\SFTP` method directly on a `Remote` object:

``` php
<?php namespace App\Http\Controllers;

use PhanAn\Remote\Remote;

class RemoteController extends Controller {

    public function index()
    {
        $connection = new Remote();

        // Of course you can specify an configured environment name, like this
        // $connection = new Remote('staging');

        // Create a file with some dummy content
        $connection->put('dodge', 'Much remote so convenience wow.');

        // List a dir
        $connection->exec('ls -a');
    }

}

```

Check phpseclib's official [SFTP Feature List](http://phpseclib.sourceforge.net/sftp/intro.html) for what you can do.

## Todo

Write tests.

## License

MIT @ phanan.