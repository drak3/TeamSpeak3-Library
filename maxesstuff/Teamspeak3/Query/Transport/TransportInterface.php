<?php

declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query\Transport;
/**
 *
 * @author drak3
 */
interface TransportInterface
{
    /**
     * Connects to the Server
     */
    public function connect();
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
    public function getAllEvents();
    
    /**
     * Sends a command to the query and returns the result plus all occured events
     * @param \maxesstuff\Teamspeak3\Query\Command $command
     * @return array Array in form Array("events"=>Array(Event e1, Event e2,...) "response"=>CommandResponse resp) 
     */
    public function sendCommand( \maxesstuff\Teamspeak3\Query\Command $command );
    
    /**
     * Waits until an event occurs
     * This method is blocking, it returns only if a event occurs, so avoid calling this method if you aren't registered to any events
     * @return array array of all occured events (e.g if two events occur together it is possible to get 2 events) 
     */
    public function waitForEvent();

    public function disconnect();
}

?>
