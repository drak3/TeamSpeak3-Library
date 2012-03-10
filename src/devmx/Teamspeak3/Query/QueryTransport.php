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
namespace devmx\Teamspeak3\Query;
use devmx\Transmission\TransmissionInterface;
use devmx\Teamspeak3\Query\Transport\CommandTranslatorInterface;
use devmx\Teamspeak3\Query\Transport\ResponseHandlerInterface;

/**
 * Abstraction of the Teamspeak3-Query
 * @author drak3
 */
class QueryTransport implements \devmx\Teamspeak3\Query\Transport\TransportInterface
{
        
    /**
     * The Transmission between us and the query
     * @var TransmissionInterface 
     */
    protected $transmission;
    
    /**
     * The CommandTranslator implementation
     * @var CommandTranslatorInterface 
     */
    protected $commandTranslator;
    
    /**
     * The ResponseHandler implementation
     * @var ResponseHandlerInterface 
     */
    protected $responseHandler;
    
    /**
     * If we received a valid ts3 ident
     * @var type 
     */
    protected $isConnected = FALSE;
    
    /**
     * Events that got received while sending a command and where not yet returned by one of the *Event methods
     * @var type 
     */
    protected $pendingEvents = Array();
    
    /**
     * Constructor
     * @param TransmissionInterface $transmission
     * @param CommandTranslatorInterface $translator
     * @param ResponseHandlerInterface $responseHandler 
     */
    public function __construct(TransmissionInterface $transmission, CommandTranslatorInterface $translator, ResponseHandlerInterface $responseHandler) {
        $this->transmission = $transmission;
        $this->commandTranslator = $translator;
        $this->responseHandler = $responseHandler;
        
    }
    
    /**
     * Sets a new CommandTranslator
     * @param CommandTranslatorInterface $translator 
     */
    public function setTranslator(CommandTranslatorInterface $translator) {
        $this->commandTranslator = $translator;
    }
    
    /**
     * Returns the used translator
     * @return CommandTranslatorInterface
     */
    public function getTranslator() {
        return $this->commandTranslator;
    }
    
    /**
     * Sets a new ResponseHandler
     * @param ResponseHandlerInterface $handler 
     */
    public function setHandler(ResponseHandlerInterface $handler) {
        $this->responseHandler = $handler;
    }
    
    /**
     * Returns the used ResponseHandler
     * @return ResponseHandlerInterface
     */
    public function getHandler() {
        return $this->responseHandler;
    }
    
    /**
     * Returns the transmission between us and the query
     * @return TransmissionInterface
     */
    public function getTransmission() {
        return $this->transmission;
    }
    
    /** 
     * Connects the query
     */
    public function connect() {
        try {
           $this->transmission->establish();
            $this->checkWelcomeMessage();
            $this->isConnected = TRUE; 
        } catch(Exception\ExceptionInterface $e) {
            throw $e;
        } catch(\devmx\Transmission\Exception\ExceptionInterface $e) {
            throw $e;
        } catch(\Exception $e) {
            throw new Exception\RuntimeException(sprintf("Cannot connect to server on %s:%d", $this->transmission->getHost(), $this->transmission->getPort()), 0, $e);
        }
        
    }
    
    /**
     * Returns wether the transport is connected to a server or not
     * @return boolean 
     */
    public function isConnected() {
        return $this->isConnected;
    }
    
    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * @param boolean $dryRun if this is true just the internal event storage, 
     * where events occured before call to sendCommand are stored, is checked.
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents($dryRun=FALSE)
    {  
        if(!$dryRun) {
            if(!$this->isConnected()) {
                throw new Exception\NotConnectedException("Cannot get events, not connected");
            }
            $response = $this->transmission->checkForData();
            if ( $response )
            {
                while ( !$this->responseHandler->isCompleteEvent( $response ) )
                {
                    $response .= $this->transmission->receiveLine();
                }
            } else {
                return;
            }
            $events = array_merge($this->pendingEvents, $this->responseHandler->getEventInstances( $response ));
            $this->pendingEvents = Array();
           return $events;
        }
        else {
            $events = $this->pendingEvents;
            $this->pendingEvents = Array();
            return $events;
        }
    }
    
    /**
     * Sends a command to the query and returns the result
     * All occured events are stored internaly, and can be get via getAllEvents
     * @param Command $command
     * @return CommandResponse
     */
    public function sendCommand( Command $command )
    {
        if(!$this->isConnected()) {
            throw new Exception\NotConnectedException("Cannot send command, not connected");
        }
        
        $data = '';

        $this->transmission->send( $this->commandTranslator->translate( $command ) );

        while ( !$this->responseHandler->isCompleteResponse( $data ) )
        {
            $data .= $this->transmission->receiveLine();
        }

        $responses = $this->responseHandler->getResponseInstance( $command , $data );
        
        $this->pendingEvents = array_merge($this->pendingEvents,$responses['events']);
        return $responses['response'];
    }
    
    /**
     * Wrapper for new Command and sendcommand
     * @param string $cmdname the name of the command
     * @param array $params the arguments of the command
     * @param array $options the options of the command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function query($cmdname, array $params=Array(),array $options=Array()) {
        return $this->sendCommand(new Command($cmdname , $params , $options));
    }
    
    /**
     * Waits until an event occurs
     * This method is blocking, it returns only if a event occurs, so avoid calling this method if you aren't registered to any events
     * @param float the timeout in second how long to wait for an event. If there is no event after the given timeout, an empty array is returned
     *   -1 means that the method may wait forever
     * @return array array of all occured events (e.g if two events occur together it is possible to get 2 events) 
     */
    public function waitForEvent($timeout=-1)
    {
        if(!$this->isConnected()) {
            throw new Exception\NotConnectedException("Cannot wait for event: not connected");
        }
        if($this->pendingEvents !== Array()) {
            $events =  $this->pendingEvents;
            $this->pendingEvents = Array();
            return $events;
        }
        
        $response = '';
        try {
            while ( !$this->responseHandler->isCompleteEvent( $response )) {   
                $response .= $this->transmission->receiveLine($timeout);
            }
        } catch( \devmx\Transmission\Exception\TimeoutException $e) {
             if($response === '' && $e->getData() == '') {
                 return array();
             }
             throw $e;
        }
        
        $events = $this->responseHandler->getEventInstances( $response );
        return $events;
    }
    
    /**
     * Disconnects from a server 
     */
    public function disconnect() {
        // because disconnect could be also called on invalid servers, we just send the quit message and don't wait for any response
        $this->transmission->send("quit\n");
        $this->transmission->close();
        $this->isConnected = FALSE;
    }
    
    /**
     * Checks the welcome message
     * @throws \RuntimeException if the welcomemessage is not valid
     */
    protected function checkWelcomeMessage()
    {
        $ident = $this->transmission->receiveLine();
        if ( !$this->responseHandler->isValidQueryIdentifyer( $ident ) )
        {
            $this->disconnect();
            throw new Exception\InvalidServerException( sprintf("Server is not valid. (Identifyer: %s)", $ident) );
        }
        $this->transmission->receiveData( $this->responseHandler->getWelcomeMessageLength() - strlen($ident));
    }
    
    /**
     * Clones the transport 
     */
    public function __clone() {
        $this->transmission = clone $this->transmission;
    }
    
    
}

?>
