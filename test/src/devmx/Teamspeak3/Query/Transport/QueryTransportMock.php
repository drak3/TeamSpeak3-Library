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
namespace devmx\Test\Teamspeak3\Query\Transport;

/**
 *
 * @author drak3
 */
class QueryTransportMock implements \devmx\Teamspeak3\Query\Transport\TransportInterface
{
    protected $isConnected = false;
    protected $events = array();
    protected $responses = array();
    
    
    public function addResponse(\devmx\Teamspeak3\Query\CommandResponse $r, $times=1 ) {
        for($i=0;$i<$times; $i++) {
            $this->responses[] = $r;
        }
    }
    
    public function addEvent(\devmx\Teamspeak3\Query\Event $e, $times=1, $newCharge=false) {
        $index = $newCharge ? count($this->events) : count($this->events) - 1;
        for($i=0; $i<$times; $i++) {
            $this->events[$index][] = $e;
        }
    }
    
    /**
     * Connects to the Server
     */
    public function connect() {
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
            throw new \LogicException('Cannot send command, not connected');
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
     * @param \devmx\Teamspeak3\Query\Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(\devmx\Teamspeak3\Query\Command $command) {
        if(!$this->isConnected()) {
            throw new \LogicException('Cannot send command, not connected');
        }
        foreach($this->responses as $key=>$possibleResponse) {
            if($possibleResponse->getCommand()->equals($command)) {
                unset($this->responses[$key]);
                return $possibleResponse;
            }
        }
        throw new \LogicException('No suitable response for command '.$command);
    }
    
    /**
     * Wrapper for new Command and sendcommand
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function query($name, array $args=Array(),array $options=Array()) {
        return $this->sendCommand(new \devmx\Teamspeak3\Query\Command($name, $args, $options));
    }
    
    /**
     * Waits until an event occurs
     * This method is blocking, it returns only if a event occurs, so avoid calling this method if you aren't registered to any events
     * @return array array of all occured events (e.g if two events occur together it is possible to get 2 events) 
     */
    public function waitForEvent() {
        return $this->getAllEvents();
    }

    public function disconnect() {
        $this->isConnected = false;
    }
        
}

?>
