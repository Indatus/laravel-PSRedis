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
    protected $driverUnderTest;

    protected $masters;

    protected $masterDiscovery;

    protected $HAClient;

    protected $PSRedisClient;

    public function setUp()
    {
        parent::setUp();

        $this->masters = [
            [
                'host' => 'host1.domain.com',
                'port' => '222222',
            ],
            [
                'host' => 'host2.domain.com',
                'port' => '222222',
            ]
        ];

        $this->masterDiscovery = m::mock('PSRedis\MasterDiscovery');
        $this->app->instance('PSRedis\MasterDiscovery', $this->masterDiscovery);

        $this->HAClient = m::mock('PSRedis\HAClient');
        $this->app->instance('PSRedis\HAClient', $this->HAClient);

        $this->PSRedisClient = m::mock('PSRedis\Client');
        $this->app->instance('PSRedis\Client', $this->PSRedisClient);
    }

    /**
     * @test
     */
    public function it_instantiates()
    {
        Config::shouldReceive('get')->once()->with('Indatus/LaravelPSRedis::nodeSetName')->andReturn('node-set');
        Config::shouldReceive('get')->once()->with('Indatus/LaravelPSRedis::masters')->andReturn($this->masters);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\MasterDiscover', ['node-set'])
            ->andReturn($this->masterDiscovery);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\Client', ['host1.domain.com', '222222'])
            ->andReturn($this->PSRedisClient);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\Client', ['host2.domain.com', '222222'])
            ->andReturn($this->PSRedisClient);

        App::shouldReceive('make')
            ->once()
            ->with('PSRedis\HAClient', [$this->masterDiscovery])
            ->andReturn($this->HAClient);

        $this->driverUnderTest = new Driver();
    }
}