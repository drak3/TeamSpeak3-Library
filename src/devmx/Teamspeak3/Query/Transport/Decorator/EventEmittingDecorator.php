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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use devmx\Teamspeak3\Query\Transport\TransportInterface;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\Response\CommandResponse;
use devmx\Teamspeak3\Query\Transport\Decorator\Event\CommandFilterEvent;
use devmx\Teamspeak3\Query\Transport\Decorator\Event\QueryTransportEvent;
use devmx\Teamspeak3\Query\Transport\Decorator\Event\QueryEventsEvent;
use devmx\Teamspeak3\Query\Transport\Decorator\Event\ResponseEvent;

/**
 * This Decorator notifies about important events on the transport
 * You can subscribe for an event by a call to addListener($eventName, $listener)
 * Thereby $listener is a callable which will be called with a Event object
 * This are all event names with corresponding event classes: 
 *    Name                  Event Class           Description
 *    query.connect         QueryTransportEvent   emitted after connect
 *    query.disconnect      QueryTransportEvent   emitted after disconnect
 *    query.filter-command  CommandFilterEvent    emitted before a command is sent, allows listeners to modify the command before sending
 *    query.response        ResponseEvent         emitted after a response is received, listeners can modify the response that will be returned to the caller
 *    query.events          QueryEventsEvent      emitted after events are received, listeners can modify the events that will be returned to the caller
 * 
 * Internally, the EventEmittingDecorator uses Symfony's EventDispatcher Component
 * @author drak3
 */
class EventEmittingDecorator extends EventDispatcher implements TransportInterface
{
    /**
     * @var TransportInterface
     */
    protected $transport;
    
    public function __construct(TransportInterface $transport) {
        $this->transport = $transport;
    }   
    
    /**
     * Connects to the Server
     */
    public function connect()
    {
        $this->transport->connect();
        $this->dispatch('query.connect', new Event\QueryTransportEvent($this));
    }
    
    /**
     * Disconnects from the server 
     */
    public function disconnect()
    {
        $this->transport->disconnect();
        $this->dispatch('query.disconnect', new Event\QueryTransportEvent($this));
    }
    
    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * @return array Array of all events lying on the query  
     */
    public function getAllEvents()
    {
        $events = $this->decorated->getAllEvents();
        return $this->filterEvents($events);
    }
    
    /**
     * Sends a command to the query and returns the result plus all occured events
     * @param Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(Command $command)
    {
        $command = $this->filterCommand($command);
        return $this->filterResponse($this->decorated->sendCommand($command));
    }
    
    protected function filterCommand(Command $command) {
        return $this->dispatch('query.filter-command', new CommandFilterEvent($this, $command))->getCommand();
    }
    
    protected function filterResponse(Response $response) {
        return $this->dispatch('query.response', new ResponseEvent($this, $response))->getResponse();
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
        $events = $this->decorated->waitForEvent($timeout);
        return $this->filterEvents($events);
    }
    
    /**
     * Filters an array of events by dispatching them to the listeners of query.events
     * @param array $events
     * @return array
     */
    protected function filterEvents(array $events) {
        $filtered = $this->dispatch('query.events', new QueryEventsEvent($events));
        return $filtered->getEvents();
    }
    
    
    //unchanged methods from AbstractQueryDecorator
    //might be solved by traits when 5.4 is mandatory
    
    /**
     * Clones the decorator
     */
    public function __clone() {
        $this->decorated = clone $this->decorated;
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
     * Returns wether the transport is connected to a server or not
     * @return boolean 
     */
    public function isConnected()
    {
        return $this->decorated->isConnected();
    }
}

?>
