<?php namespace Indatus\LaravelPSRedis;

use Illuminate\Support\ServiceProvider;
use Illuminate\Redis\Database;
use Illuminate\Session\SessionManager;

class LaravelPSRedisServiceProvider extends ServiceProvider {

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
		$this->app->singleton('redis', function () {
			$driver = new Driver();
			return new Database($driver->getConfig());
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('redis');
	}

}
