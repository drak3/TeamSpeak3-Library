<?php
/*
  This file is part of TeamSpeak3 Library.

  TeamSpeak3 Library is free software: you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  TeamSpeak3 Library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with TeamSpeak3 Library. If not, see <http://www.gnu.org/licenses/>.
 */
namespace devmx\Teamspeak3\Query\Transport;
use \devmx\Teamspeak3\Query\Exception;
use \devmx\Teamspeak3\Query\CommandResponse;
use \devmx\Teamspeak3\Query\Event;
use \devmx\Teamspeak3\Query\Command;

/**
 * This class is a implementation of the TransportInterface that holds no real connections to the outside.
 * It mainly provides methods for easy unittesting.
 * @author drak3
 */
class QueryTransportStub implements \devmx\Teamspeak3\Query\Transport\TransportInterface
{
    /**
     * If the query is connected or not
     * @var boolean
     */
    protected $isConnected = false;
    
    /**
     * Events that should be delivered
     * @var array of array of \devmx\Teamspeak3\Query\Events 
     */
    protected $events = array(array());
    
    /**
     * Responses that should be received
     * @var array of \devmx\Teamspeak3\Query\CommandResponse
     */
    protected $responses = array();
    
    /**
     * If we are expecting to be connected
     * @var boolean
     */
    protected $expectConnection = true;
    
    /**
     * Adds a response for a specific command, so it could be received by sendCommand
     * @param CommandResponse $r the response to be received
     * @param int $times how often the response should be available
     */
    public function addResponse(CommandResponse $r, $times=1 ) {
        for($i=0;$i<$times; $i++) {
            $this->responses[] = $r;
        }
    }
    
    /**
     * Adds multiple responses at once
     * @param array of \devmx\Teamspeak3\Query\CommandResponse $responses responses to add
     */
    public function addResponses(array $responses) {
        foreach($responses as $response) {
            $this->addResponse($response);
        } 
    }
    
    /**
     * Adds a event to be received either by getAllEvents or waitForEvent
     * The events are organized in charges, if you add a event to a new charge, it will be returned by call after that invocation which returned the previous charge
     * @param Event $e the event to add
     * @param int $times how often the event should be added
     * @param boolean $newCharge if this is set to true, the event will be added to a new charge
     */
    public function addEvent(Event $e, $times=1, $newCharge=false) {
        $events = array();
        for($i=0; $i<$times; $i++) {
            $events[] = $e;
        }
        if($newCharge) {
            $this->events[] = $events;
        }
        else {
            $index = count($this->events)-1;
            $this->events[$index] = array_merge($this->events[$index], $events);
        }
    }
    
    /**
     * If you call this method with false a exception is thrown on a connect call
     * @param boolean $expect 
     */
    public function expectConnection($expect=true) {
        $this->expectConnection = $expect;
    }
    
    /**
     * Connects the query
     */
    public function connect() {
        if(!$this->expectConnection) {
            throw new Exception\LogicException("No connect expected");
        }
        $this->isConnected = true;
    }
    
    /**
     * Returns wether the transport is connected to a server or not
     * @return boolean 
     */
    public function isConnected() {
        return $this->isConnected;
    }

    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents() {
        if(!$this->isConnected()) {
            throw new Exception\LogicException('Cannot get events, not connected');
        }
        if(!isset($this->events[0])) {
            return array();
        }
        else {
            return array_shift($this->events);
        }
    }

    /**
     * Sends a command to the query and returns the result plus all occured events
     * @param Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(Command $command) {
        if(!$this->isConnected()) {
            throw new Exception\LogicException('Cannot send command, not connected');
        }
        foreach($this->responses as $key=>$possibleResponse) {
            if($possibleResponse->getCommand()->equals($command)) {
                unset($this->responses[$key]);
                return $possibleResponse;
            }
        }
        throw new Exception\LogicException('No suitable response for command '.$command->getName());
    }
    
    /**
     * Wrapper for new Command and sendcommand
     * @param string $name the name of the command
     * @param array $args the arguments of the command
     * @param array $options the options of the command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function query($name, array $args=Array(),array $options=Array()) {
        return $this->sendCommand(new \devmx\Teamspeak3\Query\Command($name, $args, $options));
    }
    
    /**
     * Waits until an event occurs
     * This method is blocking, it returns only if a event occurs, so avoid calling this method if you aren't registered to any events
     * @param float the timeout in second how long to wait for an event. If there is no event after the given timeout, an empty array is returned
     *   -1 means that the method may wait forever
     * @return array array of all occured events (e.g if two events occur together it is possible to get 2 events) 
     */
    public function waitForEvent($timeout=-1) {
        $events = $this->getAllEvents();
        if($events === array())  {
            throw new Exception\LogicException('cannot wait for events');
        }
        return $events;
    }
    
    /**
     * Disconnects the query 
     */
    public function disconnect() {
        $this->isConnected = false;
    }
    
    /**
     * This method checks if all responses were received, if not, it throws an LogicException
     * @throws Exception\LogicException 
     */
    public function assertAllResponsesReceived() {
        if(!empty($this->responses)) {
            throw new Exception\LogicException('Assertion that all responses are received failed');
        }
    }
    
    /**
     * Wakes the query up 
     */
    public function __wakeup() {
            $this->connect();
    }
        
}

?>
