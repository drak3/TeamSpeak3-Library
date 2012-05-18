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
namespace devmx\Teamspeak3;
use devmx\Transmission\TCP;
use devmx\Teamspeak3\Query\Transport\QueryTransport;
use devmx\Teamspeak3\Query\Transport\Common\CommandTranslator;
use devmx\Teamspeak3\Query\Transport\Common\ResponseHandler;
use devmx\Teamspeak3\Query\ServerQuery;
use devmx\Teamspeak3\Query\CommandAwareQuery;
use devmx\Teamspeak3\SimpleContainer\DecoratorContainer;

/**
 *
 * @author drak3
 */
class SimpleContainer extends \Pimple
{
    public function __construct($host=null, $queryPort=10011, $debug=false) {
        if($host !== null) {
            $this['host'] = $host;
            $this['query.port'] = $queryPort;
        }
        $this['debug'] = $debug;
        $this->configure();
    }
    
    protected function configure() {
        
        $this['query'] = $this->share(function($c){
            return new CommandAwareQuery($c['query.serverquery']);
        });
        
        $this['query.serverquery'] = $this->share(function($c){
            return new Query\ServerQuery($c['query.transport']);
        });
        
        $this['query.transport'] = $this->share(function($c){
            return $c['query.transport.decorators']['decorated'];
        });
        
        $this['query.transport.decorators'] = new DecoratorContainer();
        
        $this['query.transport.decorators']['order'] = array(
            'caching.in_memory',
        );
        
        if($this['debug']) {
            $this['query.transport.decorators']['order'] = $this['query.transport.decorators']['order'] + array('profiling', 'debugging');
        }
        
        $that = $this;               
        $this['query.transport.undecorated'] = $this->share(function($c) use ($that){
            return new QueryTransport($that['query.transport.transmission'], $that['query.transport.translator'], $that['query.transport.handler']);
        });
        
        $this['query.transport.decorators']['undecorated'] = $this->raw('query.transport.undecorated');
        
        $this['query.transport.transmission'] = $this->share(function($c){
            return new TCP($c['host'], $c['query.port']);
        });
        
        $this['query.transport.translator'] = $this->share(function($c){
            return new CommandTranslator();
        });
        
        $this['query.transport.handler'] = $this->share(function($c){
            return new ResponseHandler();
        });
                
    }
}


?>
