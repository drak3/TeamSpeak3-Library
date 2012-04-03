<?php

namespace devmx\Teamspeak3\Query\Transport\Decorator\Caching;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\CommandResponse;

require_once dirname( __FILE__ ) . '/../../../../../../../../src/devmx/Teamspeak3/Query/Transport/Decorator/Caching/CachingDecorator.php';

/**
 * Test class for CachingDecorator.
 * Generated by PHPUnit on 2012-03-31 at 11:41:55.
 */
class CachingDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CachingDecorator
     */
    protected $decorator;
    
    /**
     * @var \devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator
     */
    protected $cache;
    
    /**
     * @var \devmx\Teamspeak3\Query\Transport\QueryTransportStub
     */
    protected $query;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->cache = $this->getMockForAbstractClass('\devmx\Teamspeak3\Query\Transport\Decorator\Caching\CacheInterface');
        $this->query = new \devmx\Teamspeak3\Query\Transport\QueryTransportStub();
        $this->decorator = new CachingDecorator($this->query, $this->cache);

    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator::connect
     * A call to connect should be delayed.
     */
    public function testConnect()
    {
        $this->query->expectConnection(false);
        $this->decorator->connect();
    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator::sendCommand
     * @todo Implement testSendCommand().
     */
    public function testSendCommand_delayed()
    {
        $this->decorator->setDelayableCommands(array('use'));
        $this->decorator->setCacheableCommands(array());
        $this->query->expectConnection(false);
        $this->decorator->sendCommand(new Command('use'));
    }
    
    public function testSendCommand_applyDelayed() {
        $this->decorator->setDelayableCommands(array('use'));
        $this->decorator->setCacheableCommands(array());
        
        $use_cmd = new Command('use');
        $use_r = new CommandResponse($use_cmd);
        $cl_cmd = new Command('channellist');
        $cl_r = new CommandResponse($cl_cmd);
        
        $this->query->expectConnection(false);
        
        $this->assertFalse($this->decorator->sendCommand($use_cmd)->errorOccured());
        
        $this->query->expectConnection();
        
        $this->query->addResponses(array($use_r, $cl_r));
        
        $this->assertEquals($cl_r , $this->decorator->sendCommand($cl_cmd));
    }
    
    /**
     * @expectedException \devmx\Teamspeak3\Query\Exception\CommandFailedException 
     */
    public function testSendCommand_applyDelayed_Error() {
        $this->decorator->setDelayableCommands(array('use'));
        $this->decorator->setCacheableCommands(array());
        
        $use_cmd = new Command('use');
        $use_r = new CommandResponse($use_cmd, array(), 123, 'error');
        $cl_cmd = new Command('channellist');
        $cl_r = new CommandResponse($cl_cmd);
        
        $this->query->expectConnection(false);
        
        $this->assertFalse($this->decorator->sendCommand($use_cmd)->errorOccured());
        
        $this->query->expectConnection();
        
        $this->query->addResponses(array($use_r, $cl_r));
        
        $this->decorator->sendCommand($cl_cmd);
    }
    
    public function testSendCommand_cache() {
        $this->decorator->setDelayableCommands(array());
        $this->decorator->setCacheableCommands(array('clientlist'));
        
        $cl_cmd = new Command('clientlist');
        $cl_r = new CommandResponse($cl_cmd);
        
        $this->query->addResponse($cl_r);
        
        $this->cache->expects($this->exactly(2))
                    ->method('isCached')
                    ->with(md5(serialize($cl_cmd)))
                    ->will($this->onConsecutiveCalls(false, true));
        
        $this->cache->expects($this->once())
                    ->method('cache')
                    ->with($this->equalTo(md5(serialize($cl_cmd))), $this->equalTo($cl_r));
        
        $this->cache->expects($this->once())
                    ->method('getCache')
                    ->with($this->equalto(md5(serialize($cl_cmd))))
                    ->will($this->returnValue($cl_r));
        
        $this->assertEquals($cl_r, $this->decorator->sendCommand($cl_cmd));
        $this->assertEquals($cl_r, $this->decorator->sendCommand($cl_cmd));
        
        $this->query->assertAllResponsesReceived();
    }
    
    public function testSendCommand_dontApplyDelayedBeforeCachedCommand() {
        $this->decorator->setDelayableCommands(array('use'));
        $this->decorator->setCacheableCommands(array('channellist'));
        
        $use_cmd = new Command('use');
        $use_r = new CommandResponse($use_cmd);
        $cl_cmd = new Command('channellist');
        $cl_r = new CommandResponse($cl_cmd);
        
        $this->query->expectConnection(false);
                
        $this->assertFalse($this->decorator->sendCommand($use_cmd)->errorOccured());
        
        $this->cache->expects($this->once())
                    ->method('isCached')
                    ->with(md5(serialize($cl_cmd)))
                    ->will($this->returnValue(true));
        
        $this->cache->expects($this->once())
                    ->method('getCache')
                    ->will($this->returnValue($cl_r));
        
        
        $this->assertEquals($cl_r, $this->decorator->sendCommand($cl_cmd));
    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator::getAllEvents
     * @todo Implement testGetAllEvents().
     */
    public function testGetAllEvents()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );

    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator::waitForEvent
     * @todo Implement testWaitForEvent().
     */
    public function testWaitForEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );

    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator::getDelayableCommands
     * @todo Implement testGetDelayableCommands().
     */
    public function testSetGetDelayableCommands()
    {
        $this->decorator->setDelayableCommands(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->decorator->getDelayableCommands());
    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator::getCacheAbleCommands
     * @todo Implement testGetCacheAbleCommands().
     */
    public function testSetGetCacheAbleCommands()
    {
        $this->decorator->setCacheableCommands(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->decorator->getCacheableCommands());
    }

}

?>
