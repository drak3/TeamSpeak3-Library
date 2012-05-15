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
use devmx\Teamspeak3\Query\CommandAwareQuery;

/**
 * This decorator caches command and their responses, to avoid the network overhead
 * Commands given with setDelayedCommands are delayed until the connection has to be opened 
 * (either by sending a command which should not be cached or by calling waitForEvent() of getAllEvents())
 * Currently more complex caching strategies like multiple caches for multiple vServers are not implemented.
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
     * The names of the commands which should be cached
     * @var array of string 
     */
    protected $cacheableCommands = array();    
    
    /**
     * Constructor
     * @param TransportInterface $toDecorate
     * @param CacheInterface $cache 
     */
    public function __construct(TransportInterface $toDecorate, CacheInterface $cache)
    {
        parent::__construct($toDecorate);
        $this->cache = $cache;
        
        //set some reasonable defaults
        $this->cacheableCommands = CommandAwareQuery::getNonChangingCommands();
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
            //we actually have to send the command
            $response = $this->decorated->sendCommand($command);
            $this->sentCommand = true;
            if($this->shouldBeCached($command)) {
                $this->cache->cache($key, $response);
            }
            
            return $response;
        }
    }
    
    /**
     * Checks if the given Command should be cached
     * @param Command $cmd
     * @return boolean 
     */
    protected function shouldBeCached(Command $cmd) {
        if(in_array($cmd->getName(), $this->getCacheableCommands())) {
            return true;
        }
        return false;
    }
    
    /**
     * Returns an array with the names of all commands which should be cached
     * @return array of string
     */
    public function getCacheAbleCommands() {
        return $this->cacheableCommands;
    }
    
    /**
     * Sets which commands should be cached
     * @param array  $commands the names of the cacheable commands
     */
    public function setCacheableCommands(array $commandNames) {
        $this->cacheableCommands = $commandNames;
    }
    
}

?>
