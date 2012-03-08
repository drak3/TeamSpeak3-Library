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
use devmx\Teamspeak3\Query\Transport\Decorator\Caching;
use devmx\Teamspeak3\Query\Transport;
use devmx\Teamspeak3\Query\Transport\TransportInterface;
use devmx\Teamspeak3\Query\Command;

/**
 * This decorator caches command and their responses, to avoid the network overhead
 * @author Maximilian Narr 
 */
class CachingDecorator extends Transport\AbstractQueryDecorator
{
    /**
     * The caching implementation
     * @var \devmx\Teamspeak3\Query\Decorator\Caching\CachingInterface
     */
    protected $cache;
    
    /**
     * Constructor
     * @param TransportInterface $toDecorate
     * @param CachingInterface $cache 
     */
    public function __construct(TransportInterface $toDecorate, CachingInterface $cache)
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
            if (!$this->decorated->isConnected())
            {
                $this->decorated->connect();
            }

            $ret = $this->decorated->sendCommand($command);

            $this->cache->cache($key, $ret);
            return $ret;
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
     * @return array 
     */
    public function waitForEvent()
    {
        if(!$this->decorated->isConnected())
            $this->decorated->connect ();
        
        return $this->decorated->waitForEvent();
    }
}

?>
