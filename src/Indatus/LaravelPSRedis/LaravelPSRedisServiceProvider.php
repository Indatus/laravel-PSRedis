<?php namespace Indatus\LaravelPSRedis;

use Illuminate\Support\ServiceProvider;
use Illuminate\Redis\Database;
use Config;
use App;

class LaravelPSRedisServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/** @var \Indatus\LaravelPSRedis\Driver $driver */
	protected $driver;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('indatus/laravel-ps-redis', 'Indatus/LaravelPSRedis');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('redis', function ($app) {
			if ( ($app['config']['queue.default'] === 'redis') && ( ! App::environment('testing')) ) {
				$this->driver = new Driver();

				if ( ! is_null($this->driver)) {
					return new Database($this->driver->getConfig());
				}
			}
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('laravel-PSRedis.psredis');
	}

}
