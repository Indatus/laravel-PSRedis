<?php
namespace LaravelPSRedis;

use Illuminate\Redis\RedisManager;
use Illuminate\Support\ServiceProvider;

class LaravelPSRedisServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'redis',
            function () {
                $driver = new Driver();
                return new RedisManager('predis', $driver->getConfig());
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['redis'];
    }

}
