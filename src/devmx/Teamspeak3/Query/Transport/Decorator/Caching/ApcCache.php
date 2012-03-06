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

/**
 * Apc caching backend
 * @author Maximilian Narr 
 */
class ApcCache implements CachingInterface
{
    /**
     * If the apc-extension is available or not
     * @var boolean
     */
    private $isAvailable;
    
    /**
     * The time items should be cached
     * @var int 
     */
    private $cacheTime;
    
    /**
     * Constructor
     * @param int $cachetime the time items should be cached
     */
    public function __construct($cachetime=180)
    {
        if(extension_loaded("apc"))
        {
            $this->isAvailable = true;
        }
        else
        {
            $this->isAvailable = false;
        }
        
        $this->cacheTime = $cachetime;
    }
    
    /**
     * Caches a data which can be accessed with key in the cache. 
     * @param string $key identifier of the data
     * @param mixed $data data to cache
     * @param int $ttl Time to live (cachetime) 
     * @return boolean true if success else false
     */
    public function cache($key, $data, $ttl=NULL)
    {
        if(!$this->isAvailable)
            return false;
        
        if($ttl == NULL)
            $ttl = $this->cacheTime;
        
        return apc_store($key, serialize($data), $ttl);
    }
    
    /**
     * Returns the cached object
     * @param string $key identifier of the data
     * @return mixed data on success else false 
     */
    public function getCache($key)
    {
        if(!$this->isAvailable)
            return false;
        
        return unserialize(apc_fetch($key));
    }
    
    /**
     * Deletes a key from the cache
     * @param string $key key to delete from cache 
     * @return bool true if success else false
     */
    public function flush($key)
    {
        if(!$this->isAvailable)
            return false;
        
        return apc_delete($key);
    }

    /**
     * Flushes the whole cache 
     * @return bool true if success else false
     */
    public function flushCache()
    {
        if(!$this->isAvailable)
            return false;
        
        return apc_clear_cache();
    }

    /**
     * If a specific key is cached
     * @return bool true if cached else false 
     */
    public function isCached($key)
    {
        if(!$this->isAvailable)
            return false;
        
        return apc_exists($key);
    }

}

?>
