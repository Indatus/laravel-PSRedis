<?php namespace Tests\Indatus\LaravelPSRedis;

use Indatus\LaravelPSRedis\Driver;
use Mockery as m;
use App;
use Config;

/**
 * Class DriverTest
 *
 * @copyright  Indatus 2014
 * @author     Damien Russell <drussell@indatus.com>
 */
class DriverTest extends BaseTest
{
    /** @var \Indatus\LaravelPSRedis\Driver */
    protected $driverUnderTest;

    /** @var array */
    protected $masters;

    /** @var \Mockery\MockInterface */
    protected $masterDiscovery;

    /** @var \Mockery\MockInterface */
    protected $HAClient;

    /** @var \Mockery\MockInterface */
    protected $PSRedisClient;

    /** @var array */
    protected $backOffStrategy;

    /** @var \Mockery\MockInterface */
    protected $incremental;

    public function setUp()
    {
        parent::setUp();

        // some of the classes from the PSRedis package implement the
        // `__call()`  methods and define a default value for the method arguments
        // php doesn't like while using the `E_STRICT` error level
        if(defined('E_STRICT')) {
            error_reporting('E_ALL ^ E_STRICT');
        }

        $this->masters = [
            [
                'host' => 'host1.domain.com',
                'port' => '222222'
            ],
            [
                'host' => 'host2.domain.com',
                'port' => '222222'
            ]
        ];

        $this->backOffStrategy = [
            'max-attempts' => 10, // the maximum-number of attempt possible to find master
            'wait-time' => 500,   // miliseconds to wait for the next attempt
            'increment' => 1.5, // multiplier used to increment the back off time on each try
        ];

        $this->masterDiscovery = m::mock('PSRedis\MasterDiscovery')->makePartial();
        $this->app->instance('PSRedis\MasterDiscovery', $this->masterDiscovery);

        $this->HAClient = m::mock('PSRedis\HAClient')->makePartial();
        $this->app->instance('PSRedis\HAClient', $this->HAClient);

        $this->PSRedisClient = m::mock('PSRedis\Client');
        $this->app->instance('PSRedis\Client', $this->PSRedisClient);
    }

    /**
     * Can we instantiate the class?
     *
     * @test
     */
    public function it_instantiates()
    {
        Config::shouldReceive('get')->once()->with('database.redis.nodeSetName')->andReturn('node-set');
        Config::shouldReceive('get')->once()->with('database.redis.masters')->andReturn($this->masters);
        Config::shouldReceive('get')->with('database.redis.backoff-strategy')->andReturn($this->backOffStrategy);

        $this->driverUnderTest = new Driver();

        $this->assertInstanceOf('Indatus\LaravelPSRedis\Driver', $this->driverUnderTest);
    }

    /**
     * Can it get the proper config array?
     *
     * @test
     */
    public function it_gets_config_values()
    {
        $expectedConfig = [
            'cluster' => false,
            'default' => [
                'host' => $this->masters[0]['host'],
                'port' => $this->masters[0]['port']
            ]
        ];

        Config::shouldReceive('get')->once()->with('database.redis.nodeSetName')->andReturn('node-set');
        Config::shouldReceive('get')->once()->with('database.redis.masters')->andReturn($this->masters);
        Config::shouldReceive('get')->with('database.redis.backoff-strategy')->andReturn($this->backOffStrategy);
        Config::shouldReceive('get')->with('database.redis.cluster')->andReturn(false);

        $this->HAClient->shouldReceive('getIpAddress')->once()->andReturn($this->masters[0]['host']);
        $this->HAClient->shouldReceive('getPort')->once()->andReturn($this->masters[0]['port']);

        $this->driverUnderTest = new Driver();

        $configUnderTest = $this->driverUnderTest->getConfig();

        $this->assertEquals($expectedConfig, $configUnderTest);
    }
}