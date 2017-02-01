<?php

namespace LaravelPSRedis;

use Illuminate\Support\Facades\Config;
use PSRedis\Client as PSRedisClient;
use PSRedis\HAClient;
use PSRedis\MasterDiscovery;
use PSRedis\MasterDiscovery\BackoffStrategy\Incremental;

class Driver
{
    /**
     * @var string Root path to configs
     */
    protected $rootConfigPath = null;

    /**
     * @var MasterDiscovery $masterDiscovery The mechanism for determining the master
     */
    protected $masterDiscovery;

    /**
     * @var HAClient $HAClient is the highly available client which handles the auto-failover.
     */
    protected $HAClient;


    /**
     * Constructor
     *
     * @param string $rootConfigPath
     */
    public function __construct($rootConfigPath = 'database.redis')
    {
        $this->rootConfigPath = $rootConfigPath;
        $this->setUpMasterDiscovery($this->getSettings('nodeSetName'));
        $this->addSentinels($this->getSettings('masters'));

        $this->HAClient = new HAClient(
            $this->masterDiscovery
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
            'cluster' => $this->getSettings('cluster'),
            'default' => [
                'host'     => $this->HAClient->getIpAddress(),
                'port'     => $this->HAClient->getPort(),
                'password' => $this->getSettings('password', null),
                'database' => $this->getSettings('database', 0),
            ]
        ];
    }

    /**
     * Define back-off strategy
     *
     * @return Incremental
     */
    protected function getBackOffStrategy()
    {
        /** @var array $backOffConfig */
        $backOffConfig = $this->getSettings('backoff-strategy');

        /** @var Incremental $incrementalBackOff */
        $incrementalBackOff = new Incremental(
            $backOffConfig['wait-time'],
            $backOffConfig['increment']
        );

        $incrementalBackOff->setMaxAttempts($backOffConfig['max-attempts']);

        return $incrementalBackOff;
    }

    /**
     * Create master discovery
     *
     * @param $nodeSetName string e.g. `mymaster`
     */
    protected function setUpMasterDiscovery($nodeSetName)
    {
        $this->masterDiscovery = new MasterDiscovery($nodeSetName);

        $this->masterDiscovery->setBackoffStrategy($this->getBackOffStrategy());
    }

    /**
     * Add sentinel to master discovery
     *
     * @param $clients list of clients
     */
    protected function addSentinels($clients)
    {
        foreach ($clients as $client) {
            $sentinel = new PSRedisClient($client['host'], $client['port']);

            $this->masterDiscovery->addSentinel($sentinel);
        }
    }

    /**
     * Get settings
     *
     * @param $name
     * @param $default
     *
     * @return mixed
     */
    protected function getSettings($name, $default)
    {
        return Config::get($this->rootConfigPath . '.' . $name, $default);
    }
}
