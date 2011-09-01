<?php
declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query\Transport;

/**
 * Base class for an QueryTransport decorator
 * The concrete class just have to overwrite the methods it wants
 * @author drak3
 */
abstract class AbstractQueryDecorator implements TransportInterface
{
    
    protected $decorated;
    
    public function __construct(TransportInterface $toDecorate) {
        $this->decorated = $toDecorate;
    }
    
    public function connect()
    {
        return $this->decorated->connect();
    }

    public function disconnect()
    {
        return $this->decorated->disconnect();
    }

    public function getAllEvents()
    {
        return $this->decorated->getAllEvents();
    }

    public function isConnected()
    {
        return $this->decorated->isConnected();
    }

    public function sendCommand( \maxesstuff\Teamspeak3\Query\Command $command )
    {
        return $this->decorated->sendCommand($command);
    }

    public function waitForEvent()
    {
        return $this->decorated->waitForEvent();
    }

    
}

?>
