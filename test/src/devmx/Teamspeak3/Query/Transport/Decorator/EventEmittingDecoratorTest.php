<?php

namespace devmx\Teamspeak3\Query\Transport\Decorator;
use devmx\Teamspeak3\Query\Transport\QueryTransportStub;
use devmx\Teamspeak3\Query\Response\Event;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\Response\CommandResponse;

class ListenerStub {
    
    protected $validator;
    
    protected $calls = 0;
    
    public function setValidator($validator) {
        $this->validator = $validator;
    } 
    
    public function listen($event){
        $this->calls++;
        call_user_func($this->validator, $event);
    }
    
    public function assertCalled() {
        if($this->calls === 0) {
            throw new \Exception('listen not called');
        }
    }
}

/**
 * Test class for EventEmittingDecorator.
 * Generated by PHPUnit on 2012-07-07 at 23:03:45.
 */
class EventEmittingDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EventEmittingDecorator
     */
    protected $decorator;
    
    /**
     * @var QueryTransportStub
     */
    protected $stub;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->stub = new QueryTransportStub();
        $this->decorator = new EventEmittingDecorator($this->stub);
        
    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\EventEmittingDecorator::connect
     * @todo Implement testConnect().
     */
    public function testConnect()
    {
        $that = $this;
        
        $listener = new ListenerStub();
        $listener->setValidator(function($event) use ($that) {
            $that->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\Event\QueryTransportEvent', $event);
        });
        $this->decorator->addListener('query.connect', array($listener, 'listen'));
        $this->decorator->connect();
        $listener->assertCalled();
                 
    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\EventEmittingDecorator::disconnect
     * @todo Implement testDisconnect().
     */
    public function testDisconnect()
    {
        $that = $this;
        
        $listener = new ListenerStub();
        $listener->setValidator(function($event) use ($that) {
            $that->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\Event\QueryTransportEvent', $event);
        });
        $this->decorator->addListener('query.disconnect', array($listener, 'listen'));
        $this->decorator->disconnect();
        $listener->assertCalled();
    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\EventEmittingDecorator::getAllEvents
     * @todo Implement testGetAllEvents().
     */
    public function testGetAllEvents()
    {
        $that = $this;
        
        $e = new Event('foobar', array());
        $e2 = new Event('asdf', array());
        $this->stub->addEvent($e);
        
        
        $this->decorator->addListener('query.events',function($event) use ($e, $e2, $that) {
           $that->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\Event\QueryEventsEvent', $event);
           $that->assertEquals(array($e), $event->getEvents());
           $event->setEvents(array($e, $e2));
        });
        
        $this->decorator->connect();
        $this->assertEquals(array($e, $e2), $this->decorator->getAllEvents());
    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\EventEmittingDecorator::sendCommand
     * @todo Implement testSendCommand().
     */
    public function testSendCommand()
    {
        $that = $this;
        
        $cmd = new Command('foo');
        $filteredCmd = new Command('bar');
        
        $response = new CommandResponse($filteredCmd);
        $filteredResponse = new CommandResponse($cmd);
        
        $this->stub->addResponse($response);
        
        $this->decorator->addListener('query.filter-command', function($event) use ($cmd, $filteredCmd, $that) {
            $that->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\Event\CommandFilterEvent', $event);
            $that->assertEquals($cmd, $event->getCommand());
            $event->setCommand($filteredCmd);
        });
        
        $this->decorator->addListener('query.response', function($event) use ($response, $filteredResponse, $that) {
            $that->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\Event\ResponseEvent', $event);
            $that->assertEquals($response, $event->getResponse());
            $event->setResponse($filteredResponse);
        });
        
        $this->decorator->connect();
        $this->assertEquals($filteredResponse, $this->decorator->sendCommand($cmd));

    }

    /**
     * @covers devmx\Teamspeak3\Query\Transport\Decorator\EventEmittingDecorator::waitForEvent
     * @todo Implement testWaitForEvent().
     */
    public function testWaitForEvent()
    {
        $that = $this;
        
        $e = new Event('foobar', array());
        $e2 = new Event('asdf', array());
        $this->stub->addEvent($e);
        
        
        $this->decorator->addListener('query.events',function($event) use ($e, $e2, $that) {
           $that->assertInstanceOf('\devmx\Teamspeak3\Query\Transport\Decorator\Event\QueryEventsEvent', $event);
           $that->assertEquals(array($e), $event->getEvents());
           $event->setEvents(array($e, $e2));
        });
        
        $this->decorator->connect();
        $this->assertEquals(array($e, $e2), $this->decorator->waitForEvent());
    }

}

?>
