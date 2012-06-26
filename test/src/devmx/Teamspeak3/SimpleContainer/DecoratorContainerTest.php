<?php
namespace devmx\Teamspeak3\SimpleContainer;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\Response\CommandResponse;
/**
 * Test class for DecoratorContainer.
 * Generated by PHPUnit on 2012-05-17 at 15:44:11.
 */
class DecoratorContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DecoratorContainer
     */
    protected $container;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->container = new DecoratorContainer();
        $this->container['undecorated'] = $this->getMockForAbstractClass('\devmx\Teamspeak3\Query\Transport\TransportInterface');
    }

    public function testBasicCreation() {
        $this->container['order'] = array('caching.in_memory');
        $this->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\CachingDecorator', $this->container['decorated']);
    }
    
    public function testFullCreation() {
        if(PHP_VERSION_ID >= 50310 && PHP_VERSION_ID <= 50313) {
            //$this->markTestSkipped("This test will segfault out of unknown reasons");
        }
        $this->container['order'] = array('caching.in_memory', 'profiling', 'debugging');
        $this->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\DebuggingDecorator', $this->container['decorated']);
    }
    
    public function testExternalDecorator() {
        if(PHP_VERSION_ID >= 50310 && PHP_VERSION_ID <= 50313) {
            $this->markTestSkipped("This test will segfault out of unknown reasons");
        }
        $this->container['order'] = array('stub1', 'profiling', 'debugging', 'stub2');
        $that = $this;
        $this->container['stub1'] = $this->container->share(function($c) use ($that) {
          return $that->getMockBuilder('\devmx\Teamspeak3\Query\Transport\Decorator\AbstractQueryDecorator')
                      ->setConstructorArgs(array($c['_prev']('stub1', $c)))
                      ->getMockForAbstractClass();  
        });
        
        $this->container['stub2'] = $this->container->share(function($c) use ($that) {
          return $that->getMockBuilder('\devmx\Teamspeak3\Query\Transport\Decorator\AbstractQueryDecorator')
                      ->setConstructorArgs(array($c['_prev']('stub2', $c)))
                      ->getMockForAbstractClass();  
        });
        
        $cmd = new Command('asdf');
        $r = new CommandResponse($cmd);
        

        $this->container['undecorated']->expects($this->once())
                                       ->method('sendCommand')
                                       ->with($this->equalTo($cmd))
                                       ->will($this->returnValue($r));
        $this->assertEquals($r, $this->container['decorated']->sendCommand($cmd));
        $this->assertEquals(1, $this->container['profiling']->getNumberOfSentCommands());
    }
}

?>
