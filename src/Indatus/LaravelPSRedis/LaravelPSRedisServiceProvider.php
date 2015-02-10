<?php namespace Indatus\LaravelPSRedis;

use Illuminate\Support\ServiceProvider;
use Illuminate\Redis\Database;

class LaravelPSRedisServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

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

		// this has to be done in the boot method inorder for the driver class to
		// have access to the config values
		$this->driver = new Driver();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('redis', function($app)
		{
			return new Database($this->driver->getConfig());
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
