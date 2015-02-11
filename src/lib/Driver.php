<?php namespace Indatus\LaravelPSRedis;

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
    public function __construct()
    {
        $this->masterDiscovery = App::make(
            'PSRedis\MasterDiscovery',
            [Config::get('Indatus/LaravelPSRedis::nodeSetName')]
        );

        $clients = Config::get('Indatus/LaravelPSRedis::masters');
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
            'cluster' => Config::get('Indatus/LaravelPSRedis::cluster'),
            'default' => [
                'host' => $this->HAClient->getIpAddress(),
                'port' => $this->HAClient->getPort()
            ]
        ];
    }
}
