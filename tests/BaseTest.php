<?php  namespace Tests\Indatus\LaravelPSRedis;

use Illuminate\Foundation\Testing\TestCase;
use Mockery as m;

/**
 * Class BaseTest
 *
 * @copyright  Indatus 2014
 * @author     Damien Russell <drussell@indatus.com>
 */
abstract class BaseTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    /**
     * Create the application
     *
     * @return mixed
     */
    public function createApplication()
    {
        $unitTesting = true;
        $testEnvironment = 'testing';

        return require __DIR__ . '/../../../../bootstrap/start.php';
    }

    public function tearDown()
    {
        m::close();
    }
}