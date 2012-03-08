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
namespace devmx\Teamspeak3\Query\Transport\Decorator\Logging;
use devmx\Teamspeak3\Query\Transport\Decorator\Logging;
use devmx\Teamspeak3\Query\Transport;
use devmx\Teamspeak3\Query\Transport\TransportInterface;
use devmx\Teamspeak3\Query\Command;

/**
 * This QueryDecorator Logs all interesting events happenening on the query
 * Therefore it uses a LoggingInterface as backend and logs events with different loglevels:
 * LOGGING_LEVEL_ERROR: Exceptions occuring (Exceptions are rethrown after logging)
 * LOGGING_LEVEL_WARNING: Failed commands
 * LOGGING_LEVEL_INFO: All successful operations like connecting, sending command,...
 * @author Maximilian Narr 
 */
class LoggingQueryDecorator extends Transport\AbstractQueryDecorator
{
    
    /**
     * The concrete logging implementations
     * @var LoggingInterface 
     */
    protected $logger;
    
    /**
     * Constructor
     * @param TransportInterface $toDecorate
     * @param LoggingInterface $logger 
     */
    public function __construct(TransportInterface $toDecorate, LoggingInterface $logger)
    {
        parent::__construct($toDecorate);
        $this->logger = $logger;
    }
    
    /**
     * Cpmmects tp the server
     */
    public function connect()
    {

        try
        {
            $ret = $this->decorated->connect();
        }
        catch (\RuntimeException $ex)
        {
            $this->logger->addLog($this->getExceptionDetails($ex), LoggingInterface::LOGGING_LEVEL_ERROR);
            throw $ex;
        }

        $this->logger->addLog("Successfully connected to ServerQuery", LoggingInterface::LOGGING_LEVEL_INFO);
        return $ret;
    }

    /**
     * Disconnects from the server
     */
    public function disconnect()
    {
        try
        {
            $ret = $this->decorated->disconnect();
        }
        catch (\RuntimeException $ex)
        {
            $this->logger->addLog($this->getExceptionDetails($ex), LoggingInterface::LOGGING_LEVEL_ERROR);
            throw $ex;
        }

        $this->logger->addLog("Successfully disconnected from ServerQuery", LoggingInterface::LOGGING_LEVEL_INFO);
        return $ret;
    }
    
    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents()
    {
        try
        {
            $ret = $this->decorated->getAllEvents();
        }
        catch (\RuntimeException $ex)
        {
            $this->logger->addLog($this->getExceptionDetails($ex), LoggingInterface::LOGGING_LEVEL_ERROR);
            throw $ex;
        }

        $this->logger->addLog("Received all occured events from server", LoggingInterface::LOGGING_LEVEL_INFO);
        return $ret;
    }
    
    /**
     * Returns wether the transport is connected to a server or not
     * @return boolean 
     */
    public function isConnected()
    {
        $connected = $this->decorated->isConnected();

        if (!$connected)
        {
            $this->logger->addLog("No longer connected to ServerQuery", LoggingInterface::LOGGING_LEVEL_INFO);
            return false;
        }
        else
        {
            $this->logger->addLog("Connected to ServerQuery", LoggingInterface::LOGGING_LEVEL_INFO);
            return true;
        }
    }
    
    /**
     * Sends a command to the query and returns the result plus all occured events
     * @param Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(Command $command)
    {
        try
        {
            $ret = $this->decorated->sendCommand($command);
        }
        catch (\RuntimeException $ex)
        {
            $this->logger->addLog($this->getExceptionDetails($ex), LoggingInterface::LOGGING_LEVEL_ERROR);
            throw $ex;
        }

        if ($ret->errorOccured())
        {
            $this->logger->addLog(sprintf("Failed executing command: %s: %s (%s)", $ret->getCommand(), $ret->getErrorMessage(), $ret->getErrorID()), LoggingInterface::LOGGING_LEVEL_WARNING);
        }
        
        $this->logger->addLog(sprintf("Successfully executed command: %s"), LoggingInterface::LOGGING_LEVEL_INFO);
        return $ret;
    }
    
    /**
     * Waits until an event occurs
     * This method is blocking, it returns only if a event occurs, so avoid calling this method if you aren't registered to any events
     * @return array array of all occured events (e.g if two events occur together it is possible to get 2 events) 
     */
    public function waitForEvent()
    {
        try
        {
            $ret = $this->decorated->waitForEvent();
        }
        catch (\RuntimeException $ex)
        {
            $this->logger->addLog($this->getExceptionDetails($ex), LoggingInterface::LOGGING_LEVEL_ERROR);
        }
        
        $this->logger->addLog("Successfully waited for next event", LoggingInterface::LOGGING_LEVEL_INFO);
        
        return $ret;
    }

    /**
     * Returns a string containing the exception message, file and line
     * @param Exception $ex
     * @return string Exception ready for log
     */
    protected function getExceptionDetails(\Exception $ex)
    {
        return $ex->getMessage() . " in " . $ex->getFile() . " on line " . $ex->getLine();
    }

}

?>
