<?php  namespace Tests\Indatus\LaravelPSRedis;

use Illuminate\Foundation\Testing\TestCase;

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
        return require __DIR__ . '/../../../../bootstrap/start.php';
    }
}