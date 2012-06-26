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
namespace devmx\Teamspeak3\Query\Transport\Decorator\Caching\Cache;
use devmx\Teamspeak3\Query\Transport\Decorator\Caching\CacheInterface;

/**
 *
 * @author drak3
 */
class NullCache implements CacheInterface
{
    /**
     * Caches a data which can be accessed with key in the cache. 
     * @param string $key identifier of the data
     * @param mixed $data data to cache
     * @param int $ttl Time to live (cachetime) if null is supplied, a default value should be used
     * @return boolean true if success else false
     */
    public function cache($key, $data, $ttl=null) {
        return true;
    }
    
    /**
     * Returns the cached object
     * @param string $key identifier of the data
     * @return mixed data on success else false 
     */
    public function getCache($key) {
        return false;
    }
   
    /**
     * If a specific key is cached
     * @param string $key the key to lookup
     * @return bool true if cached else false 
     */
    public function isCached($key) {
        return false;
    }
}

?>
