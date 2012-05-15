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
namespace devmx\Teamspeak3\Query\Transport\Decorator;
use devmx\Teamspeak3\Query\Transport\TransportInterface;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\CommandAwareQuery;
use devmx\Teamspeak3\Query\CommandResponse;

/**
 * This decorator delays the connection and the sending of specific commands until a not delayable Command is sent, or events are requested.
 * @author drak3
 */
class DelayingDecorator extends \devmx\Teamspeak3\Query\Transport\AbstractQueryDecorator
{   
    /**
     * Name of the Commands that could be delayed
     * @var array of string
     */
    protected $delayableCommands = array();
    
    /**
     * The currently delayed commands
     * @var array of Command
     */
    protected $delayedCommands = array();
    
    /**
     * Constructor
     * @param TransportInterface $t 
     */
    public function __construct(TransportInterface $t) {
        parent::__construct($t);
        $this->delayableCommands = CommandAwareQuery::getQueryStateChangingCommands();
    }
    
    /**
     * Connects to the Server
     * As we are delaying the connect as long as possible this method does actually nothing
     */
    public function connect()
    {
        return;
    }
    
    /**
     * Disconnects from the server 
     */
    public function disconnect()
    {
        if ($this->decorated->isConnected())
        {
            $this->decorated->disconnect();
        }
        else
        {
            return;
        }
    }
    
    /**
     * Sends a command to the query and returns the result plus all occured events
     * If the command is delayed a succesfull response without items is returned
     * @param Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(Command $command) {
        if($this->shouldBeDelayed($command) && !$this->decorated->isConnected())  {
                $this->delay($command);
                //If we are delaying the query connection, we return a successfull response
                return new CommandResponse($command);
        }
        $this->setUpConnection();
        return $this->decorated->sendCommand($command);
    }
    
    /**
     * Sets which commands should be delayed
     * @param array $commands the names of the delayable commands
     */
    public function setDelayAbleCommands(array $commands) {
        $this->delayableCommands = $commands;
    }
    
    /**
     * Returns an array with the names of all commands which should be delayed
     * @return array of string
     */
    public function getDelayAbleCommands() {
        return $this->delayableCommands;
    }
    
    /**
     * Waits for a event on the query
     * this mehtod is blocking
     * All delayed commands are sent before the events are get
     * @param float the timeout in second how long to wait for an event. If there is no event after the given timeout, an empty array is returned
     *   -1 means that the method may wait forever
     * @return array 
     */
    public function waitForEvent($timeout=-1)
    {
        $this->setUpConnection();
        
        return $this->decorated->waitForEvent($timeout);
    }
    
    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * All delayed commands are sent before the events are get
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents()
    {
        $this->setUpConnection();
        
        return $this->decorated->getAllEvents();
    }
    
    /**
     * Checks if the given Command should be delayed
     * @param Command $cmd
     * @return boolean 
     */
    protected function shouldBeDelayed(Command $cmd) {
        if(in_array($cmd->getName(), $this->getDelayableCommands())) {
            return true;
        }
        return false;
    }
    
    /**
     * Stores a command for later sending
     * @param Command $cmd 
     */
    protected function delay(Command $cmd) {
        $this->delayedCommands[] = $cmd;
    }
    
    /**
     * Sends all currently applied commands and clears the internal delayedCommands buffer 
     */
    protected function sendDelayedCommands() {
        foreach($this->delayedCommands as $cmd) {
            $response = $this->decorated->sendCommand($cmd);
            $this->checkDelayedResponse($response);
        }
        $this->delayedCommands = array();
    }
    
    /**
     * Sets up the connection (connect+send delayed commands) 
     */
    protected function setUpConnection() {
        if(!$this->decorated->isConnected()) {
                $this->decorated->connect();
        }
        $this->sendDelayedCommands();
    }
    
    /**
     * Checks if the response was successful
     * @throws \devmx\Teamspeak3\Query\Exception\CommandFailedException
     * @param CommandResponse $response 
     */
    protected function checkDelayedResponse(CommandResponse $response) {
        $response->toException();
    }
}

?>
