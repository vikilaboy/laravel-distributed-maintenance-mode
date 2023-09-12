<p align="center">
<a href="https://packagist.org/packages/despark/laravel-distributed-maintenance-mode"><img src="https://img.shields.io/packagist/dt/despark/laravel-distributed-maintenance-mode" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/despark/laravel-distributed-maintenance-mode"><img src="https://img.shields.io/packagist/v/despark/laravel-distributed-maintenance-mode" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/despark/laravel-distributed-maintenance-mode"><img src="https://img.shields.io/packagist/l/despark/laravel-distributed-maintenance-mode" alt="License"></a>
</p>

## About Laravel Maintenance Mode

Laravel's maintenance mode allows a developer to take a website offline(`php artisan down`) for maintenance or updates while still allowing selected users, such as administrators, to access the site. When maintenance mode is enabled, all non-authorized users will be shown a customizable maintenance page instead of the website's content.
Once maintenance is complete, the developer can disable maintenance mode(`php artisan up`) and the website will return to normal operation. This feature can be useful for preventing users from accessing a website while updates or changes are being made, and also allows a developer to perform maintenance on a live website without disrupting the user experience.

## What It Does
The way maintenance mode works out of the box though is quite limited for most major application setups. The default configuration is to store a file on the disk which assumes that you have a single server serving your application or that you are willing to SSH into all of your servers to run the command.
The other default option is to store the maintenance information on your default cache configuration. This again has a major limitation, because most applications clear their default cache once a server is deployed, which will wipe the maintenance mode.

Here comes Laravel Distributed Maintenance Mode, which gives you the ability to choose from two drivers Redis or S3. After that you have to select either the preferred Redis database or S3 bucket to store the maintenance info on.

By doing that, you just need to run the default `php artisan down` command on one of your servers. The maintenance mode will be picked up by all of them and also being persisted on any of the new ones that you deploy until you lift it up by `php artisan up`.

## Installation 

1) Install with [composer](https://getcomposer.org/doc/00-intro.md)

```
composer require despark/laravel-distributed-maintenance-mode
```

2) Publish the configuration file

```
php artisan vendor:publish --tag="laravel-distributed-maintenance-mode.config"
```

## Usage Instructions

1) Open the `config/app.php` file and change the maintenance mode driver to `custom`

```php
'maintenance' => [
    'driver' => 'custom',   
],
```

2) Open the `config/laravel-distributed-maintenance-mode.php` file and change the maintenance mode driver to your preferred one(`redis` or `s3`).

3) If you have selected `redis` as your driver, set your preferred Redis database in which you want to store the information. It must already be set up in `config/database.php` under the `redis` configuration

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'predis'),
        'prefix' => env('REDIS_PREFIX', \Illuminate\Support\Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
    'maintenance_mode' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_MAINTENANCE_MODE_DB', '2'),
    ],
],
```

4) If you have selected `s3` as your driver, set your preferred S3 disk in which you want to store the information. It must already be set up in `config/filesystems.php` under the `disks` configuration

```php
 'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'throw' => false,
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        'throw' => false,
    ],
    'maintenance_mode_s3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET_MAINTENANCE_MODE'),
        'url' => env('AWS_URL_MAINTENANCE_MODE'),
        'endpoint' => env('AWS_ENDPOINT_MAINTENANCE_MODE'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        'throw' => false,
    ],
],
```

5) You are now ready. You can use the default `up` and `down` artisan commands to turn off/on the maintenance mode of your application from only one server

## License

The Laravel Distributed Maintenance Mode is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
