<?php namespace Indatus\LaravelPSRedis;

use Illuminate\Foundation\Application;
use PSRedis\Client as PSRedisClient;
use PSRedis\MasterDiscovery;
use PSRedis\HAClient;
use Config;
use App;

/**
 * Class Driver
 *
 * @copyright  Indatus 2014
 * @author     Damien Russell <drussell@indatus.com>
 */
class Driver
{
    /** @var MasterDiscovery $masterDiscovery The mechanism for determining the master */
    protected $masterDiscovery;

    /** @var HAClient $HAClient is the highly available client which handles the auto-failover. */
    protected $HAClient;


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->masterDiscovery = App::make(
            'PSRedis\MasterDiscovery',
            [$this->app['config']['database.redis.nodeSetName']]
        );

        $clients = $this->app['config']['database.redis.masters'];
        foreach($clients as $client) {
            $sentinel = App::make(
                'PSRedis\Client',
                [$client['host'], $client['port']]
            );

            $this->masterDiscovery->addSentinel($sentinel);
        }

        $this->HAClient = App::make(
            'PSRedis\HAClient',
            [$this->masterDiscovery]
        );
    }


    /**
     * Get the config values for the redis database.
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'cluster' => $this->app['config']['database.redis.cluster'],
            'default' => [
                'host' => $this->HAClient->getIpAddress(),
                'port' => $this->HAClient->getPort()
            ]
        ];
    }
}
