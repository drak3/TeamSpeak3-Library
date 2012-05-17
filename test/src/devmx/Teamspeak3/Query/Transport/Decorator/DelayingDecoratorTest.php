<?php

namespace devmx\Teamspeak3\Query\Transport\Decorator;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\CommandResponse;
use devmx\Teamspeak3\Query\CommandAwareQuery;

/**
 * Test class for DelayingDecorator.
 * Generated by PHPUnit on 2012-05-15 at 17:32:05.
 */
class DelayingDecoratorTest extends \PHPUnit_Framework_TestCase
{

    protected $cache;
    protected $query;
    protected $decorator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->cache = $this->getMockForAbstractClass('\devmx\Teamspeak3\Query\Transport\Decorator\Caching\CacheInterface');
        $this->query = new \devmx\Teamspeak3\Query\Transport\QueryTransportStub();
        $this->decorator = new DelayingDecorator($this->query);
    }

    public function testSendCommand_delayed()
    {
        $this->decorator->setDelayableCommands(array('use'));
        $this->query->expectConnection(false);
        $this->decorator->sendCommand(new Command('use'));
    }
    
    public function testSendCommand_applyDelayed() {
        $this->decorator->setDelayableCommands(array('use'));
        
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
    
    /**
     * @dataProvider eventGetterProvider
     */
    public function testGetAllEvents_applyBeforeGet($method) {
        $this->decorator->setDelayableCommands(array('use'));
        $this->query->expectConnection(false);
        
        $useCommand = new Command('use');
        $useResponse = new CommandResponse($useCommand);
        $event = new \devmx\Teamspeak3\Query\Event('foo', array());
        
        $this->decorator->sendCommand($useCommand);
        
        $this->query->expectConnection();
        $this->query->addResponse($useResponse);
        $this->query->addEvent($event);
        
        $this->assertEquals(array($event), $this->decorator->$method());
        $this->query->assertAllResponsesReceived();
    }
    
    /**
     * @dataProvider eventGetterProvider
     */
    public function testGetAllEvents_connect($method)
    {
        $e = new \devmx\Teamspeak3\Query\Event('foobar', array());
        $this->query->addEvent($e);
        $this->assertEquals(array($e), $this->decorator->$method());
        $this->assertTrue($this->query->isConnected());
    }
    
    public function eventGetterProvider() {
        return array( array('getAllEvents'), array('waitForEvent') );
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
    
    public function testDefaults() {
        $this->assertEquals(CommandAwareQuery::getQueryStateChangingCommands(), $this->decorator->getDelayableCommands());
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
}

?>