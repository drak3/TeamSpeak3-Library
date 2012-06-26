<?php
namespace devmx\Teamspeak3\Query\Transport\Decorator\Caching\Cache;

/**
 * Test class for InMemoryCache.
 * Generated by PHPUnit on 2012-05-16 at 15:18:27.
 */
class InMemoryCacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InMemoryCache
     */
    protected $cache;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->cache = new InMemoryCache;

    }
    
    public function testCache()
    {
       $this->cache->cache('foo', array('bar'), 0.2);
       $this->assertEquals(array('bar'), $this->cache->getCache('foo'));
       usleep(2.1E5);
       $this->assertFalse($this->cache->getCache('foo'));
    }

}

?>
