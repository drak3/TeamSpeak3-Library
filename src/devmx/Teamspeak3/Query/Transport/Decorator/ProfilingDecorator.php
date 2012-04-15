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
namespace devmx\Teamspeak3\Query\Transport\Decorator;

use devmx\Teamspeak3\Query\Command;

/**
 * This decorator provides basic abilities to profile the Query transactions
 * @author drak3
 */
class ProfilingDecorator extends AbstractTransportDecorator
{
    /**
     * Stores an array of all times a connectcall took
     * @var array
     */
    protected $connectionTimes = array();
    
    /**
     * Stores an array of all times the disconnectcalls took
     * @var array
     */
    protected $disconnectionTimes = array();
    
    /**
     * Stores an array of all times the sendCommandcalls took
     * @var array
     */
    protected $sendCommandTimes = array();
    
    /**
     * Stores an array of all times the waitForEventcalls took
     * @var array
     */
    protected $waitForEventTimes = array();
    
    /**
     * Stores an array of all times the getAllEventscalls took
     * @var array
     */
    protected $getAllEventsTimes = array();
    
    /**
     * Returns an array of all durations of the connect() calls
     * @return array
     */
    public function getConnectionTimes() {
        return $this->connectionTimes;
    }
    
    /**
     * Returns an array of all durations of the disconnect() calls
     * @return array
     */
    public function getDisconnectionTimes() {
        return $this->disconnectionTimes;
    }
    
    /**
     * Returns an array of all durations of the sendCommand() calls
     * @return array
     */
    public function getSendCommandTimes() {
        return $this->sendCommandTimes;
    }
    
    /**
     * Returns an array of all durations of the waitForEvent() calls
     * @return array
     */
    public function getWaitForEventTimes() {
        return $this->waitForEventTimes;
    }
    
    /**
     * Returns an array of all durations of the getAllEvents() calls
     * @return array
     */
    public function getAllEventsTimes() {
        return $this->getAllEventsTimes;
    }
    
    /**
     * Returns the duration of all connect() calls together
     * @return float
     */
    public function getTotalConnectionTime() {
        return $this->getTotal($this->getConnectionTimes());
    }
    
    /**
     * Returns the duration of all disconnect() calls together
     * @return float
     */
    public function getTotalDisconnectionTime() {
        return $this->getTotal($this->getDisconnectionTimes());
    }
    
    /**
     * Returns the duration of all sendCommand() calls together
     * @return float
     */
    public function getTotalSendCommandTime() {
        return $this->getTotal($this->getSendCommandTimes());
    }
    
    /**
     * Returns the duration of all waitForEvent() calls together
     * @return float
     */
    public function getTotalWaitForEventTime() {
        return $this->getTotal($this->getWaitForEventTimes());
    }
    
    /**
     * Returns the duration of all getAllEvents() calls together
     * @return float
     */
    public function getTotalGetAllEventsTime() {
        return $this->getTotal($this->getAllEventsTimes());
    }
    
    /**
     * Returns the duration of all query actions together
     * @return float
     */
    public function getTotalTime() {
        return $this->getTotalConnectionTime() + $this->getTotalDisconnectionTime() + $this->getTotalGetAllEventsTime() + $this->getTotalSendCommandTime() + $this->getTotalWaitForEventTime();
    }
    
    /**
     * Returns the total number of connect() calls
     * @return int
     */
    public function getNumberOfConnections() {
        return count($this->getConnectionTimes());
    }
    
    /**
     * Returns the total number of disconnect() calls
     * @return int
     */
    public function getNumberOfDisconnections() {
        return count($this->getDisconnectionTimes());
    }
    
    /**
     * Returns the total number of sendCommand() calls
     * @return int 
     */
    public function getNumberOfSentCommands() {
        return count($this->getSendCommandTimes());
    }
    
    /**
     * Returns the total number of waitForEvent() calls
     * @return int
     */
    public function getNumberOfWaitForEventCalls() {
        return count($this->getWaitForEventTimes());
    }
    
    /**
     * Returns the total number of getAllEvents() calls
     * @return int
     */
    public function getNumberOfGetAllEventsCalls() {
        return count($this->getAllEventsTimes());
    }
    
    /**
     * Returns the avearage time it took to connect
     * @return float
     */
    public function getAverageConnectTime() {
        return $this->getTotalConnectionTime() / $this->getNumberOfConnections();
    }
    
    /**
     * Returns the avearage time it took to disconnect
     * @return float
     */
    public function getAverageDisconnectTime() {
        return $this->getTotalDisconnectionTime() / $this->getNumberOfDisconnections();
    }
    
    /**
     * Returns the avearage time it took to send a command
     * @return float
     */
    public function getAverageSendCommandTime() {
        return $this->getTotalSendCommandTime() / $this->getNumberOfSentCommands();
    }
    
    /**
     * Returns the avearage time it took to wait for an event
     * @return float
     */
    public function getAverageWaitForEventTime() {
        return $this->getTotalWaitForEventTime() / $this->getNumberOfWaitForEventCalls();
    }
    
    /**
     * Returns the avearage time it took to get all events
     * @return float
     */
    public function getAverageGetAllEventsTime() {
        return $this->getTotalGetAllEventsTime() / $this->getNumberOfGetAllEventsCalls();
    }    
    
    /**
     * Connects to the Server
     */
    public function connect()
    {
        $start = $this->time();
        $ret = $this->decorated->connect();
        $end = $this->time();
        $total = $end - $start;
        $this->connectionTimes[] = $total;
        return $ret;
    }
    
    /**
     * Disconnects from the server 
     */
    public function disconnect()
    {
        $start = $this->time();
        $ret = $this->decorated->disconnect();
        $end = $this->time();
        $total = $end-$start;
        $this->disconnectionTimes[] = $total;
        return $ret;
    }
    
    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents()
    {
        $start = $this->time();
        $ret = $this->decorated->getAllEvents();
        $end = $this->time();
        $total = $end-$start;
        $this->getAllEventsTimes[] = $total;
        return $ret;
    }
    
    /**
     * Sends a command to the query and returns the result plus all occured events
     * @param Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(Command $command)
    {
        $start = $this->time();
        $ret = $this->decorated->sendCommand($command);
        $end = $this->time();
        $total = $end-$start;
        $this->sendCommandTimes[] = $total;
        return $ret;
    }
    
    /**
     * Wrapper for new Command and sendcommand
     * @param string $name the name of the command
     * @param array $args the arguments of the command
     * @param array $options the options of the command
     * @todo change this to act as a real wrapper and do not delegate this
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function query($name, array $args=Array(),array $options=Array())
    {
        return $this->sendCommand(new Command($name, $args, $options));
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
        $start = $this->time();
        $ret = $this->decorated->waitForEvent($timeout);
        $end = $this->time();
        $total = $end-$start;
        $this->waitForEventTimes[] = $total;
    }
    
    /**
     * Calculates the total time of a given times array
     * @param array $times
     * @return float
     */
    protected function getTotal(array $times) {
        $total = 0;
        foreach($times as $time) {
            $total += $time;
        }
        return $total;
    }
    
    /**
     * Gives the current time in seconds
     * @return float
     */
    protected function time() {
        return \microtime(true);
    }
    
}

?>
