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

        $this->masterDiscovery = m::mock('PSRedis\MasterDiscovery');
        $this->app->instance('PSRedis\MasterDiscovery', $this->masterDiscovery);

        $this->HAClient = m::mock('PSRedis\HAClient')->makePartial();;
        $this->app->instance('PSRedis\HAClient', $this->HAClient);

        $this->PSRedisClient = m::mock('PSRedis\Client');
        $this->app->instance('PSRedis\Client', $this->PSRedisClient);

        Config::shouldReceive('get')->once()->with('Indatus/LaravelPSRedis::nodeSetName')->andReturn('node-set');
        Config::shouldReceive('get')->once()->with('Indatus/LaravelPSRedis::masters')->andReturn($this->masters);

        $this->masterDiscovery->shouldReceive('addSentinel')->twice()->with($this->PSRedisClient);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\MasterDiscovery', ['node-set'])
            ->andReturn($this->masterDiscovery);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\Client', [$this->masters[0]['host'], $this->masters[0]['port']])
            ->andReturn($this->PSRedisClient);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\Client', [$this->masters[1]['host'], $this->masters[1]['port']])
            ->andReturn($this->PSRedisClient);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\HAClient', [$this->masterDiscovery])
            ->andReturn($this->HAClient);
    }

    /**
     * The instantiates method will run before each class to ensure
     *
     * @test
     */
    public function it_instantiates()
    {
        $this->driverUnderTest = new Driver();
        $this->assertInstanceOf('Indatus\LaravelPSRedis\Driver', $this->driverUnderTest);
    }

    /**
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

        Config::shouldReceive('get')->with('Indatus/LaravelPSRedis::cluster')->andReturn(false);
        $this->HAClient->shouldReceive('getIpAddress')->once()->andReturn($this->masters[0]['host']);
        $this->HAClient->shouldReceive('getPort')->once()->andReturn($this->masters[0]['port']);

        $this->driverUnderTest = new Driver();
        $configUnderTest = $this->driverUnderTest->getConfig();

        $this->assertEquals($expectedConfig, $configUnderTest);
    }
}