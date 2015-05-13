# Laravel-PSRedis

A simple sentinel/redis driver wrapper for laravel. 

The default laravel redis driver supports redis clusters, however, it does not support high availability with redis, which is where Laravel-PSRedis comes to the rescue. 

With Laravel-PSRedis you'll get all the laravel redis magic that you aleady have such 
as `Redis::set()` and `Redis::get()`, and even session, queue, and cache support using redis,
you'll just be able to leverage High Avaliability redis instances instead of a simple cluster.

We do this by asking your [Redis Sentinels](http://redis.io/topics/sentinel) the location of your master before creating our Redis bindings in the IOC Container. By doing this we ensure anytime your app has a connection to your redis instance, that connection is to master. 

## README Contents

* [Installation](#installation)
  * [Laravel 5 Installation](#installation-for-Laravel-5)
  * [Laravel 4 Installation](#installation-for-Laravel-4)
* [Configuration](#configuration)
  * [Service Provider](#the-service-provider)
* [Contributing](#contributing);
* [Testing](#testing)
* [License](#license)

<a name="installation" />
## Installation

<a name="installation-for-Laravel-5" />
### Installation for Laravel 5

You can install Laravel-PSRedis easily with composer.

```
	"require": {  
        "indatus/laravel-ps-redis": "dev-master",
    },
```

<a name="installation-for-Laravel-4" />
### Installation for Laravel 4

If you're using Laravel 4 then the installation is slightly different. Laravel-PSRedis depends on `sparkcentral/psredis` which requires `'predis/predis': '>=1.0'` in it's stable release. I've taken the liberty of forking `sparkcentral/psredis` and rolling back `predis/predis` to `0.8.7`
which is required by laravel 4. To utilize this fork simply require both `indatus\larave-ps-redis` and `sparkcentral/psredis` in your composer.json. And add a repository to point to the fork. Like so:

```
	"repositories": [  
        {
            "type": "vcs",
            "url": "https://github.com/Olofguard/PSRedis"
        }
    ],
	"require": {  
        "indatus/laravel-ps-redis": "dev-master",
        "sparkcentral/psredis": "dev-master"        
    },
```

This will help composer form an installable set of packages, otherwise composer complains about laravel needing `predis/predis` at version `0.8.7` while `sparkcentral/psredis` is installing `1.0.*`.

<a name="configuration" />
## Configuration 

Next, just fill in your sentinel/redis server info in the `app/config/database.php` config files that already exist in your application. 

You may already have some default laravel config values in place in your database config file that looks like this.

```
/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/
	'redis' => [
		'cluster' => false,
		'default' => [
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		],
	],
``` 

Just overwrite those with the values below and fill in your server info.

```
	'redis' => [

   		/** the name of the redis node set */
    	'nodeSetName' => 'sentinel-node-set',

    	'cluster' => false,

	    /** Array of sentinels */
	    'masters' => [
	        [
	            'host' => 'sentinel-instance.domain.com',
	            'port' => '26379',
	        ],
	        [
            	'host' => 'sentinel-instance.domain.com',
        	    'port' => '26379',
    	    ]
	    ],
    
    	/** how long to wait and try again if we fail to connect to master */
	    'backoff-strategy' => [
			'max-attempts' => 10, // the maximum-number of attempt possible to find master
			'wait-time' => 500,   // miliseconds to wait for the next attempt
			'increment' => 1.5, // multiplier used to increment the back off time on each try
    	]
    ];  
```

<a name="the-service-provider" />
### The Service Provider

Finally, you just need to add the service provider to the providers array in `app.php` and comment or remove the
redis service provider. 

```
	/*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    */
	'providers' => [
		...
		// 'Illuminate\Redis\RedisServiceProvider', # comment this out
		'Indatus\LaravelPSRedis\LaravelPSRedisServiceProvider' # add this
	],
```

> Note: you may have to `composer dump-autoload` after adding the service provider

<a name="contributing" />
## Contributing

1. Fork it
2. Create your feature branch (git checkout -b my-new-feature)
3. Commit your changes (git commit -m 'Added some feature')
4. Push to the branch (git push origin my-new-feature)
5. Create new Pull Request

<a name="testing" />
## Testing

Feel free to clone the repo and run the unit tests locally. 

```
	./vendor/bin/phpunit -c ./phpunit.xml 
```

<a name="license" />
## License
[The MIT License (MIT)](https://github.com/Indatus/laravel-PSRedis/blob/master/LICENSE)
