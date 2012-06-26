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
use devmx\Teamspeak3\Query\Transport;
use devmx\Teamspeak3\Query\Transport\TransportInterface;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\CommandResponse;
use devmx\Teamspeak3\Query\CommandAwareQuery;
use devmx\Teamspeak3\Query\Transport\Decorator\Caching\CacheInterface;

/**
 * This decorator caches command and their responses, to avoid the network overhead
 * There is one cache per server.
 * Note that on unexpected movements (ban/kick, NOT on uses/deselects), the cached data might be invalid
 * @author Maximilian Narr 
 * @author drak3
 */
class CachingDecorator extends AbstractQueryDecorator
{
    
    const VSERVER_PREFIX = 'devmx.ts3.vserver';
    
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
     * The prefix the name is prefixed with
     * @var string
     */
    protected $prefix = '';
    
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
        if($command->getName() === 'use') {
            $this->updatePrefix($command->getParameters());
        }
        
        $key = $this->prefix.md5(serialize($command));

        if ($this->isCacheable($command) && $this->cache->isCached($key))
        {
            return $this->cache->getCache($key);
        }
        else
        {
            //we actually have to send the command
            $response = $this->decorated->sendCommand($command);
            $this->sentCommand = true;
            if($this->isCacheable($command)) {
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
    protected function isCacheable(Command $cmd) {
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
    
    protected function updatePrefix(array $identifyer) {
        if($identifyer === array()) {
            $this->prefix = '';
        }
        if(isset($identifyer['port'])) {
            $this->prefix = self::VSERVER_PREFIX.'.port.'.$identifyer['port'].'.';
        }
        if(isset($identifyer['id'])) {
            $this->prefix = self::VSERVER_PREFIX.'.id.'.$identifyer['id'].'.';
        }
    }
    
}

?>
