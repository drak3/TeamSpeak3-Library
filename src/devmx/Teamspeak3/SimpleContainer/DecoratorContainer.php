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
namespace devmx\Teamspeak3\SimpleContainer;
use devmx\Teamspeak3\Query\Transport\Decorator\Caching\CachingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\Caching\Cache\InMemoryCache;
use devmx\Teamspeak3\Query\Transport\Decorator\DebuggingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\ProfilingDecorator;

class DecoratorContainer extends \Pimple {
    
    public function __construct() {
        $this['decorated'] = $this->share(function($c){
             return $c['_last'];
        });
                        
        $this['caching.in_memory'] = $this->share(function($c) {
            $toDecorate = $c['_prev']('caching.in_memory', $c);
            $cache = $c['caching.in_memory.cache'];
            return new CachingDecorator($toDecorate, $cache);
        });
        
        $this['caching.in_memory.cache'] = $this->share(function($c) {
            return new InMemoryCache($c['caching.in_memory.cache.cachetime']);
        });
        
        $this['caching.in_memory.cache.cachetime'] = 0.5;
        
        $this['debugging'] = $this->share(function($c){
            return new DebuggingDecorator($c['_prev']('debugging', $c));
        });
        
        $this['profiling'] = $this->share(function($c){
            return new ProfilingDecorator($c['_prev']('profiling', $c));
        });
      
        $this['_prev'] = $this->protect(function($current, $c){
            $prev = $c['undecorated'];
            foreach($c['order'] as $name) {
                if($name === $current) {
                    return $prev;
                }
                $prev = $c[$name];
            }
            throw new \LogicException("Unkown decorator name $current");
        });
        
        $this['_last'] = $this->share(function($c){
            return $c[$c['order'][count($c['order'])-1]];          
        });
    }
}

?>
