<?php
declare(encoding="UTF-8");
namespace devmx\Teamspeak3\Query;
use devmx\Transmission\TransmissionInterface;
use devmx\Teamspeak3\Transport\CommandTranslatorInterface;
use devmx\Teamspeak3\Query\Transport\ResponseHandlerInterface;

/**
 * Abstraction of the Teamspeak3-Query
 * @author drak3
 */
class QueryTransport implements Transport\TransportInterface
{
    /**
     * Constructs a common Query which should fit to the official query
     * @param string $host the host of the Ts3-Server
     * @param int $port the Queryport of the Ts3-Server
     * @return QueryTransport 
     */
    public static function getCommon($host, $port)  {
        $trans = new \devmx\Transmission\TCP($host, $port);
        return new QueryTransport($trans, new Transport\Common\CommandTranslator(), new Transport\Common\ResponseHandler());
    }
    
    /**
     *
     * @var TransmissionInterface 
     */
    protected $transmission;
    /**
     *
     * @var CommandTranslatorInterface 
     */
    protected $translator;
    /**
     *
     * @var ResponseHandlerInterface 
     */
    protected $responseHandler;
    
    protected $pendingEvents;
    
    /**
     *
     * @param TransmissionInterface $transmission
     * @param \devmx\Teamspeak3\Query\Transport\CommandTranslatorInterface $translator
     * @param ResponseHandlerInterface $responseHandler 
     */
    public function __construct(TransmissionInterface $transmission,
                                \devmx\Teamspeak3\Query\Transport\CommandTranslatorInterface $translator,
                                ResponseHandlerInterface $responseHandler) {
        $this->transmission = $transmission;
        $this->commandTranslator = $translator;
        $this->responseHandler = $responseHandler;
        
    }
    
    /**
     * Sets a new CommandTranslator
     * @param Transport\CommandTranslatorInterface $translator 
     */
    public function setTranslator(Transport\CommandTranslatorInterface $translator) {
        $this->commandTranslator = $translator;
    }
    
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
    
    public function getHandler() {
        return $this->responseHandler;
    }
    
    public function getTransmission() {
        return $this->transmission;
    }
    
    /**
     * Connects to the Server
     */
    public function connect() {
        $this->transmission->establish();
        $this->checkWelcomeMessage();
        $this->isConnected = TRUE;
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
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents()
    {
        if(!$this->isConnected()) {
            return;
        }
        $response = $this->transmission->getAll();
        if ( $response )
        {
            while ( !$this->responseHandler->isCompleteEvent( $response ) )
            {

                $response .= $this->transmission->receiveLine();
            }
        } else
        {
            return;
        }
        $events = array_merge($this->pendingEvents, $this->responseHandler->getEventInstances( $response ));
        $this->pendingEvents = Array();
        return $events;
    }
    
    /**
     * Sends a command to the query and returns the result
     * All occured events are stored internaly, and can be get via getAllEvents
     * @param \devmx\Teamspeak3\Query\Command $command
     * @return CommandResponse
     */
    public function sendCommand( \devmx\Teamspeak3\Query\Command $command )
    {
     
        $data = '';


        $this->transmission->send( $this->commandTranslator->translate( $command ) );

        while ( !$this->responseHandler->isCompleteResponse( $data ) )
        {
            $data .= $this->transmission->receiveLine();
        }

        $responses = $this->responseHandler->getResponseInstance( $command , $data );
        
        $this->pendingEvents += $responses['events'];
        
        return $responses['response'];
    }
    
    public function query($cmdname, $params=Array(), $options=Array()) {
        $this->sendCommand(Command::simpleCommand($cmdname , $params , $options));
    }
    
    /**
     * Waits until an event occurs
     * This method is blocking, it returns only if a event occurs, so avoid calling this method if you aren't registered to any events
     * @return array array of all occured events (e.g if two events occur together it is possible to get 2 events) 
     */
    public function waitForEvent()
    {
        if($this->pendingEvents !== Array()) {
            $events =  $this->pendingEvents;
            $this->pendingEvents = Array();
            return $events;
        }
        
        $response = '';
        while ( !$this->responseHandler->isCompleteEvent( $response ))
        {
            $response .= $this->transmission->receiveLine();
        }
        $events = $this->responseHandler->getEventInstances( $response );
        return $events;
    }
    
    public function disconnect() {
        $this->transmission->close();
    }
    
    /**
     * Checks the welcome message
     * @throws \RuntimeException if the welcomemessage is not valid
     */
    protected function checkWelcomeMessage()
    {

        $welcome = $this->transmission->receiveData( $this->responseHandler->getWelcomeMessageLength() );
        if ( !$this->responseHandler->isWelcomeMessage( $welcome ) )
        {
            $this->disconnect();
            throw new \RuntimeException( "Server is not valid" );
        }

    }
    
    
}

?>
