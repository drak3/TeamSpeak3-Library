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
use devmx\Teamspeak3\Query\Transport\Decorator\CachingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\Caching\Cache\InMemoryCache;
use devmx\Teamspeak3\Query\Transport\Decorator\DebuggingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\ProfilingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\DelayingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\EventEmittingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\LoggingDecorator;
use devmx\Teamspeak3\Query\Transport\Decorator\Logging\Proxy\MonologProxy;
use Monolog\Logger;
use devmx\Teamspeak3\Query\Transport\Decorator\TickingDecorator;

class DecoratorContainer extends \Pimple {
    
    public function __construct() {
        $this['decorated'] = $this->share(function($c){
            return $c['_last'];
        });
                        
        $this['caching.in_memory'] = $this->share(function($c) {
            $toDecorate = $c->getPreviousDecorator('caching.in_memory');
            $cache = $c['caching.in_memory.cache'];
            return new CachingDecorator($toDecorate, $cache);
        });
        
        $this['caching.in_memory.cache'] = $this->share(function($c) {
            return new InMemoryCache($c['caching.in_memory.cache.cachetime']);
        });
        
        $this['caching.in_memory.cache.cachetime'] = 0.5;
        
        $this['debugging'] = $this->share(function($c){
            return new DebuggingDecorator($c->getPreviousDecorator('debugging'));
        });
        
        $this['profiling'] = $this->share(function($c){
            return new ProfilingDecorator($c->getPreviousDecorator('profiling'));
        });
        
        $this['delaying'] = $this->share(function($c){
            return new DelayingDecorator($c->getPreviousDecorator('delaying'));
        });
        
        $this['event-emitting'] = $this->share(function($c){
            return new EventEmittingDecorator($c->getPreviousDecorator('event-emitting'));
        });
        
        $this['logging'] = $this->share(function($c) {
            return new LoggingDecorator($c->getPreviousDecorator('logging') , $c['logging.logger']);
        });
        
        $this['logging.logger'] = $this->share(function($c) {
            return $c['logging.logger.monolog-proxy'];
        });
        
        $this['logging.logger.monolog-proxy'] = $this->share(function($c) {
           return new MonologProxy($c['logging.logger.monolog']);
        });
        
        $this['logging.logger.monolog'] = $this->share(function($c) {
           return new \Monolog\Logger('devmx.teamspeak3'); 
        });
        
        $this['ticking'] = $this->share(function($c) {
            $decorator = new TickingDecorator($c->getPreviousDecorator('ticking'));
            $decorator->setTickTime($c['ticking.tick-time']);
            return $decorator;
        });
        
        $this['ticking.tick-time'] = 2;
        
        $this['_last'] = $this->share(function($c){
            if(count($c['order']) === 0) {
                return $c['undecorated'];
            }
            return $c[$c['order'][count($c['order'])-1]];          
        });
    }
    
    public function getPreviousDecorator($current) {
        $prev = $this['undecorated'];
        foreach($this['order'] as $name) {
            if($name === $current) {
                return $prev;
            }
            $prev = $this[$name];
        }
        throw new \LogicException("Unkown decorator name $current");
    }
}

?>
