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
		if ( $this->shouldProvidePSRedis() ) {
			$this->app->bindShared('redis', function () {
				$driver = new Driver();
				return new Database($driver->getConfig());
			});
		}
	}

	/**
	 * Determine if we need to bind redis to the ioc container.
	 *
	 * @return bool
	 */
	public function shouldProvidePSRedis()
	{
		$configs = [
			$this->app['config']['queue.default'],
			$this->app['config']['cache.driver'],
			$this->app['config']['session.driver']
		];

		if (in_array('redis', $configs)) {
			return true;
		}

		return false;
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
