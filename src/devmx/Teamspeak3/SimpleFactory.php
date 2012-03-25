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

/**
 * This factory eases the creation of Teamspeak3 Classes in most scenarios
 * @author drak3
 */
class SimpleFactory
{
    /**
     * If we are in debug mode (currently it just indicates if the debuggingdecorator should be used or not) 
     * @var boolean 
     */
    protected $debug = false;
    
    /**
     * Constructor
     * @param boolean $debug if classes should be created with debug mode enabled 
     *                       (currently this just adds the DebuggingDecorator for querytransports)
     */
    public function __construct($debug=false) {
        $this->debug = $debug;
    }
    
    /**
     * Creates a new ServerQuery instance for the given host/port combination
     * @param string $host the host of the Teamspeak3-Server
     * @param int $port the queryport 
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function getQuery($host, $port=10011) {
        return new Query\ServerQuery($this->getQueryTransport($host , $port));
    }
    
    /**
     * Creates a new QueryTransport instance for the given host/port combination
     * @param string $host the host of the Teamspeak3-Server
     * @param int $port the queryport
     * @param boolean $decorate if the query should be decorated with some default decorators specified in decorateTransport
     * @return  \devmx\Teamspeak3\Query\QueryTransport
     */
    public function getQueryTransport($host, $port=10011, $decorate = true) {
        $transport = new Query\QueryTransport($this->getTcp($host, $port), $this->getCommandTranslator() , $this->getResponseHandler());
        if($decorate) {
            $transport = $this->decorateTransport($transport);
        }
        return $transport;
    }
    
    /**
     * Decorates the given transport with the configured decorators (via getDefaultDecorators, getDebuggingDecorators)
     * @param Query\QueryTransport $t
     * @return \devmx\Teamspeak3\Query\Transport\TransportInterface
     */
    public function decorateTransport(Query\QueryTransport $t) {
        $decorated = $t;
        foreach($this->getDecorators() as $decorator) {
            $method = 'get'.$decorator;
            if(!method_exists($this, $method)) {
                throw new \LogicException(sprintf('Unknown decorator %s', $decorator));
            }
        }
        return $decorated;
    }
    
    /**
     * Returns all decorators for the current scenario (debug or not-debug)
     * @return array of strings the names of the decorators
     */
    public function getDecorators() {
        $decorators = $this->getDefaultDecorators();
        if($this->debug) {
            $decorators = array_merge($decorators, $this->getDebuggingDecorators());
        }
        return $decorators;
    }
    
    /**
     * Returns all decorators to use in every enviroment
     * @return array of string
     */
    public function getDefaultDecorators() {
        return array();
    }
    
    /**
     * Returns all decorators to use in debug enviroment
     * @return array of string
     */
    public function getDebuggingDecorators() {
        return array('DebuggingDecorator');
    }
    
    /**
     * Returns the default CommandTranslator implementation
     * @return \Query\Transport\Common\CommandTranslator 
     */
    public function getCommandTranslator() {
        return new Query\Transport\Common\CommandTranslator();
    }
    
    /**
     * Returns the default ResponseHandler implementation
     * @return \Query\Transport\Common\ResponseHandler 
     */
    public function getResponseHandler() {
        return new Query\Transport\Common\ResponseHandler();
    }
    
    /**
     * Creates a new Tcp object
     * @param string $host
     * @param int $port
     * @return \devmx\Transmission\TCP 
     */
    public function getTcp($host, $port) {
        return new \devmx\Transmission\TCP($host, $port);
    }    
}

?>
