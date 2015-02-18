# Laravel-PSRedis

A simple sentinel/redis driver wrapper for laravel. 

The default laravel redis driver supports redis clusters, however, it does not support high availability with redis, which is where Laravel-PSRedis comes to the rescue. 

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

Once you have the package installed, it's time to configure it. First you'll need to publish the config files. 

```
	php artisan config:publish indatus/laravel-ps-redis  
```

This will add the appropriate config files to laravel's `app/config/packages` directory. 

Next, just fill in your sentinel/redis server info in the newly added config files. You should see environment speicifc directories for staging, production, and demo under `app/config/packages/indatus/laravel-ps-redis`. Feel free to add more for you specific environments like `local`

Fill in the appropriate config files here and you're almost done. 

```
return [

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
            'host' => 'another-',
            'port' => '26379',
        ]
    ]
];
```
<a name="the-service-provider" />
### The Service Provider

Finally, you just need to add the service provider to the providers array in `app.php`. 

```
	/*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    */
	'providers' => [
		...
		'Indatus\LaravelPSRedis\LaravelPSRedisServiceProvider
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
