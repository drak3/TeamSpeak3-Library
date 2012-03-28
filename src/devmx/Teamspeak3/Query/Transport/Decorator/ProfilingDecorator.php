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
 *
 * @author drak3
 */
class ProfilingDecorator extends \devmx\Teamspeak3\Query\Transport\AbstractQueryDecorator
{
    
    protected $connectionTimes = array();
    
    protected $disconnectionTimes = array();
    
    protected $sendCommandTimes = array();
    
    protected $waitForEventTimes = array();
    
    protected $getAllEventsTimes = array();
    
    public function getConnectionTimes() {
        return $this->connectionTimes;
    }
    
    public function getDisconnectionTimes() {
        return $this->disconnectionTimes;
    }
    
    public function getSendCommandTimes() {
        return $this->sendCommandTimes;
    }
    
    public function getWaitForEventTimes() {
        return $this->waitForEventTimes;
    }
    
    public function getAllEventsTimes() {
        return $this->getAllEventsTimes;
    }
    
    public function getTotalConnectionTime() {
        return $this->getTotal($this->getConnectionTimes());
    }
    
    public function getTotalDisconnectionTime() {
        return $this->getTotal($this->getDisconnectionTimes());
    }
    
    public function getTotalSendCommandTime() {
        return $this->getTotal($this->getSendCommandTimes());
    }
    
    public function getTotalWaitForEventTime() {
        return $this->getTotal($this->getWaitForEventTimes());
    }
    
    public function getTotalGetAllEventsTime() {
        return $this->getTotal($this->getAllEventsTimes());
    }
    
    public function getTotalTime() {
        return $this->getTotalConnectionTime() + $this->getTotalDisconnectionTime() + $this->getTotalGetAllEventsTime() + $this->getTotalSendCommandTime() + $this->getTotalWaitForEventTime();
    }
    
    public function getNumberOfConnections() {
        return count($this->getConnectionTimes());
    }
    
    public function getNumberOfDisconnections() {
        return count($this->getDisconnectionTimes());
    }
    
    public function getNumberOfSentCommands() {
        return count($this->getSendCommandTimes());
    }
    
    public function getNumberOfWaitForEventCalls() {
        return count($this->getWaitForEventTimes());
    }
    
    public function getNumberOfGetAllEventsCalls() {
        return count($this->getAllEventsTimes());
    }
    
    public function getAverageConnectTime() {
        return $this->getTotalConnectionTime() / $this->getNumberOfConnections();
    }
    
    public function getAverageDisconnectTime() {
        return $this->getTotalDisconnectionTime() / $this->getNumberOfDisconnections();
    }
    
    public function getAverageSendCommandTime() {
        return $this->getTotalSendCommandTime() / $this->getNumberOfSentCommands();
    }
    
    public function getAverageWaitForEventTime() {
        return $this->getTotalWaitForEventTime() / $this->getNumberOfWaitForEventCalls();
    }
    
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
