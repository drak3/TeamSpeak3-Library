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
 * Implementation of the CachingInterface using files as store
 * @author Maximilian Narr
 */
class FileCache implements CachingInterface
{
    /**
     * The directory where the cache files are stored
     * @var string
     */
    protected $cacheDirectory;
    
    /**
     * The time to live of cached values in seconds
     * @var int
     */
    protected $cacheTime;
    
    /**
     * Constructor
     * @param string $cacheDirectory the directory where the cache files are stored
     * @param int $cacheTime the default cachetime in seconds
     */
    function __construct($cacheDirectory, $cacheTime = 180)
    {
        if (substr($cacheDirectory, -1) !== "/") $cacheDirectory.="/";

        $this->cacheDirectory = $cacheDirectory;
        $this->cacheTime = $cacheTime;
    }
    
    /**
     * Caches a data which can be accessed with key in the cache. 
     * @param string $key identifier of the data
     * @param mixed $data data to cache
     * @param int $ttl Time to live (cachetime) 
     * @return boolean true if success else false
     */
    public function cache($key, $data, $ttl)
    {
        if ($ttl == NULL) $ttl = $this->cacheTime;

        file_put_contents($this->cacheDirectory . $key . ".time", time() . ";$ttl");
        return file_put_contents($this->cacheDirectory . $key, serialize($data));
    }

    /**
     * Deletes a key from the cache
     * @param string $key key to delete from cache 
     * @return bool true if success else false
     */
    public function flush($key)
    {
        if (file_exists($this->cacheDirectory . $key . ".time")) unlink($this->cacheDirectory . $key . ".time");
        if (file_exists($this->cacheDirectory . $key)) unlink($this->cacheDirectory . $key);
        return true;
    }

    /**
     * Flushes the whole cache 
     * @return bool true if success else false
     */
    public function flushCache()
    {
        $handler = opendir($this->cacheDirectory);

        while ($file = readdir($handler))
        {
            unlink($file);
        }
        return true;
    }
    
    /**
     * Returns the cached object
     * @param string $key identifier of the data
     * @return mixed data on success else false 
     */
    public function getCache($key)
    {
        if (file_exists($this->cacheDirectory . $key . ".time"))
        {
            $time = file_get_contents($this->cacheDirectory . $key . ".time");
            $time = explode(";", $time);

            if (time() - $time[0] <= $time[1])
            {
                if (file_exists($this->cacheDirectory . $key)) return unserialize(file_get_contents($this->cacheDirectory . $key));
            }
            return false;
        }
        return false;
    }
    
    /**
     * If a specific key is cached
     * @param string the identifyer of the value to check
     * @return bool true if cached else false 
     */
    public function isCached($key)
    {
        if (file_exists($this->cacheDirectory . $key . ".time"))
        {
            $time = file_get_contents($this->cacheDirectory . $key . ".time");
            $time = explode(";", $time);

            if (time() - $time[0] <= $time[1])
            {
                if (file_exists($this->cacheDirectory . $key)) return true;
            }
            return false;
        }
        return false;
    }

}

?>
