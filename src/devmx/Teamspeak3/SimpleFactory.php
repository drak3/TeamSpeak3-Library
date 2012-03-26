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
    
    protected $host;
    
    protected $port;
    
    protected $query;
    
    protected $decoratedTransport;
    
    protected $decorators = array();
    
    protected $transport;
    
    protected $handler;
    
    protected $translator;
    
    protected $tcp; 
    
    /**
     * Constructor
     * @param boolean $debug if classes should be created with debug mode enabled 
     *                       (currently this just adds the DebuggingDecorator for querytransports)
     */
    public function __construct($host, $port=10011, $debug=false) {
        $this->host = $host;
        $this->port = $port;
        $this->debug = $debug;
    }
    
    /**
     * Creates a new ServerQuery instance for the given host/port combination
     * @param string $host the host of the Teamspeak3-Server
     * @param int $port the queryport 
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function getQuery() {
        if(!($this->query instanceof Query\ServerQuery)) {
            $this->query = new Query\ServerQuery($this->getQueryTransport());
        }
        return $this->query;
    }
    
    /**
     * Creates a new QueryTransport instance for the given host/port combination
     * @param string $host the host of the Teamspeak3-Server
     * @param int $port the queryport
     * @param boolean $decorate if the query should be decorated with some default decorators specified in decorateTransport
     * @return  \devmx\Teamspeak3\Query\QueryTransport
     */
    public function getQueryTransport($decorated=true) {
        if(!($this->transport instanceof Query\Transport\TransportInterface)) {
            $this->transport = new Query\QueryTransport($this->getTcp($this->host, $this->port), $this->getCommandTranslator() , $this->getResponseHandler());
        }
        if($decorated && !($this->decoratedTransport instanceof Query\Transport\TransportInterface)) {
            $this->decorateTransport();
        }
        if($decorated) {
            return $this->decoratedTransport;
        }
        else {
            return $this->transport;
        }
    }
    
    /**
     * Decorates the given transport with the configured decorators (via getDefaultDecorators, getDebuggingDecorators)
     * @param Query\QueryTransport $t
     * @return \devmx\Teamspeak3\Query\Transport\TransportInterface
     */
    protected function  decorateTransport() {
        $decorated = $this->getQueryTransport(false);
        foreach($this->getDecorators() as $decorator) {
            $method = 'get'.$decorator;
            if(!method_exists($this, $method)) {
                throw new \LogicException(sprintf('Unknown decorator %s', $decorator));
            }
            $decorated = $this->$method();
        }
        $this->decoratedTransport = $decorated;
    }
    
    /**
     * Returns all decorators for the current scenario (debug or not-debug)
     * @return array of strings the names of the decorators
     */
    public function getDecorators() {
        $decorators = $this->getDefaultDecorators();
        if($this->debug) {
            $decorators = array_merge($decorators, $this->getDebugDecorators());
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
    public function getDebugDecorators() {
        return array('DebuggingDecorator');
    }
    
    public function getDebuggingDecorator() {
        if(!isset($this->decorators['DebuggingDecorator'])) {
            $this->decorators['DebuggingDecorator'] = new Query\Transport\Decorator\DebuggingDecorator($this->getQueryTransport(false));
        }
        return $this->decorators['DebuggingDecorator'];
    }
    
    /**
     * Returns the default CommandTranslator implementation
     * @return \Query\Transport\Common\CommandTranslator 
     */
    public function getCommandTranslator() {
        if(!($this->handler instanceof Query\Transport\CommandTranslatorInterface)) {
            $this->translator = new Query\Transport\Common\CommandTranslator();
        }
        return $this->translator;
    }
    
    /**
     * Returns the default ResponseHandler implementation
     * @return \Query\Transport\Common\ResponseHandler 
     */
    public function getResponseHandler() {
        if(!($this->handler instanceof Query\Transport\ResponseHandlerInterface)) {
            $this->handler = new Query\Transport\Common\ResponseHandler();
        }
        return $this->handler;
    }
    
    /**
     * Creates a new Tcp object
     * @param string $host
     * @param int $port
     * @return \devmx\Transmission\TCP 
     */
    public function getTcp($host, $port) {
        if(!isset($this->tcp[$host][$port]) || !($this->tcp[$host][$port] instanceof \devmx\Transmission\TransmissionInterface)) {
            $this->tcp[$host][$port] = new \devmx\Transmission\TCP($host, $port);
        }
        return $this->tcp[$host][$port];
    }    
}

?>
