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
namespace devmx\Teamspeak3\Query\Transport\Decorator\Caching;
use devmx\Teamspeak3\Query\Transport;
use devmx\Teamspeak3\Query\Transport\TransportInterface;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\CommandResponse;

/**
 * This decorator caches command and their responses, to avoid the network overhead
 * Commands given with setDelayedCommands are delayed until the connection has to be opened 
 * (either by sending a command which should not be cached or by calling waitForEvent() of getAllEvents())
 * @author Maximilian Narr 
 */
class CachingDecorator extends Transport\AbstractQueryDecorator
{
    /**
     * The caching implementation
     * @var \devmx\Teamspeak3\Query\Decorator\Caching\CachingInterface
     */
    protected $cache;
    
    protected $delayableCommands = array();
    
    protected $cacheableCommands = array();
    
    protected $delayedCommands = array();
    
    protected $appliedDelayedCommands = false;
    
    
    /**
     * Constructor
     * @param TransportInterface $toDecorate
     * @param CacheInterface $cache 
     */
    public function __construct(TransportInterface $toDecorate, CacheInterface $cache)
    {
        parent::__construct($toDecorate);
        $this->cache = $cache;
    }
    
    /**
     * Connects to the Server
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
     * If the command is cached, no query to the server will be made and the cached response is returned
     * @param Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(Command $command)
    {
        $key = md5(serialize($command));

        if ($this->cache->isCached($key))
        {
            return $this->cache->getCache($key);
        }
        else
        {
            if($this->shouldBeDelayed($command) && !$this->decorated->isConnected())  {
                $this->delay($command);
                //If we are delaying the query connection, we return a successfull response
                return new CommandResponse($command);
            }
            if(!$this->decorated->isConnected()) {
                $this->decorated->connect();
            }
            if(!$this->appliedDelayedCommands) {
                $this->applyDelayedCommands();
            }
            $response = $this->decorated->sendCommand($command);
            $this->sentCommand = true;
            if($this->shouldBeCached($command)) {
                $this->cache->cache($key, $response);
            }
            
            return $response;
        }
    }
    
    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents()
    {
        if(!$this->decorated->isConnected())
            $this->decorated->connect ();
        
        return $this->decorated->getAllEvents();
    }
    
    /**
     * Waits for a event on the query
     * this mehtod is blocking
     * @param float the timeout in second how long to wait for an event. If there is no event after the given timeout, an empty array is returned
     *   -1 means that the method may wait forever
     * @return array 
     */
    public function waitForEvent($timeout=-1)
    {
        if(!$this->decorated->isConnected())
            $this->decorated->connect ();
        
        return $this->decorated->waitForEvent($timeout);
    }
    
    protected function shouldBeDelayed(Command $cmd) {
        if(in_array($cmd->getName(), $this->getDelayableCommands())) {
            return true;
        }
        return false;
    }
    
    protected function shouldBeCached(Command $cmd) {
        if(in_array($cmd->getName(), $this->getCacheableCommands())) {
            return true;
        }
        return false;
    }
    
    protected function delay(Command $cmd) {
        $this->delayedCommands[] = $cmd;
    }
    
    protected function applyDelayedCommands() {
        foreach($this->delayedCommands as $cmd) {
            $response = $this->decorated->sendCommand($cmd);
            $this->checkDelayedResponse($response);
        }
        $this->appliedDelayedCommands = true;
    }
    
    protected function checkDelayedResponse(CommandResponse $response) {
        $response->toException();
    }
    
    public function getDelayableCommands() {
        return $this->delayableCommands;
    }
    
    public function getCacheAbleCommands() {
        return $this->cacheableCommands;
    }
    
    public function setDelayableCommands(array $commands) {
        $this->delayableCommands = $commands;
    }
    
    public function setCacheableCommands($commands) {
        $this->cacheableCommands = $commands;
    }
}

?>
