<?php

declare(strict_types=1);

namespace Vikilaboy\DistributedMaintenanceMode\Providers;

use Despark\DistributedMaintenanceMode\Drivers\RedisDriver;
use Despark\DistributedMaintenanceMode\Drivers\S3Driver;
use Despark\DistributedMaintenanceMode\Exceptions\DriverNotFound;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\MaintenanceModeManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redis;

class DistributedMaintenanceModeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/laravel-distributed-maintenance-mode.php' => config_path(
                'laravel-distributed-maintenance-mode.php'
            ),
        ], 'laravel-distributed-maintenance-mode.config');
    }

    public function register(): void
    {
        $this->app->extend(MaintenanceModeManager::class, function (MaintenanceModeManager $manager) {
            $manager->extend('custom', function (Container $container) {
                $config = $container->make(Repository::class)->get('laravel-distributed-maintenance-mode');
                $driver = match ($config['driver']) {
                    'redis' => new RedisDriver(
                        Redis::connection($config['redis']['database'])->client()
                    ),
                    's3' => new S3Driver(
                        $container->make(FilesystemManager::class),
                        $config['s3']['disk']
                    ),
                    default => null
                };

                if (is_null($driver)) {
                    throw new DriverNotFound(sprintf('Unsupported driver %s.', $config['driver']));
                }

                return $driver;
            });

            return $manager;
        }
        );
    }
}
