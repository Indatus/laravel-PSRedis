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
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('redis', function ($app) {
			if ( ($app['config']['queue.default'] === 'redis')) {
				$this->driver = new Driver($app);

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
		return array('redis');
	}

}
