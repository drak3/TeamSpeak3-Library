<?php
declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query;
use maxesstuff\Transmission\TransmissionInterface;
use maxesstuff\Teamspeak3\Transport\CommandTranslatorInterface;
use maxesstuff\Teamspeak3\Query\Transport\ResponseHandlerInterface;
/**
 * 
 *
 * @author drak3
 */
class QueryTransport
{
    
    public static function getCommon($host, $port)  {
        $trans = new \maxesstuff\Transmission\TCP($host, $port);
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
    
    public function __construct(TransmissionInterface $transmission,
                                \maxesstuff\Teamspeak3\Query\Transport\CommandTranslatorInterface $translator,
                                ResponseHandlerInterface $responseHandler) {
        $this->transmission = $transmission;
        $this->commandTranslator = $translator;
        $this->responseHandler = $responseHandler;
        
    }
    
    public function setTranslator(Transport\CommandTranslatorInterface $translator) {
        $this->commandTranslator = $translator;
    }
    
    public function setHandler(ResponseHandlerInterface $handler) {
        $this->responseHandler = $handler;
    }
    
    public function connect() {
        $this->transmission->establish();
        $this->checkWelcomeMessage();
        $this->isConnected = TRUE;
    }
    
    public function isConnected() {
        return $this->isConnected;
    }

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
        $events = $this->responseHandler->getEventInstances( $response );
    }

    public function sendCommand( \maxesstuff\Teamspeak3\Query\Command $command )
    {
     
        $data = '';


        $this->transmission->send( $this->commandTranslator->translate( $command ) );

        while ( !$this->responseHandler->isCompleteResponse( $data ) )
        {
            $data .= $this->transmission->receiveLine();
        }

        $responses = $this->responseHandler->getResponseInstance( $command , $data );

        return $responses;
    }

    public function waitForEvent()
    {
        
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
