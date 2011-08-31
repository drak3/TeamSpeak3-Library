<?php
declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query\Transport;

/**
 *
 * @author drak3
 */
interface ResponseHandlerInterface
{
    /**
     * Builds a CommandResponse from the query-returned data
     * @param \maxesstuff\Teamspeak3\Query\Command $cmd
     * @param string $raw
     * @return \maxesstuff\Teamspeak3\Query\CommandResponse
     */
    public function getResponseInstance( \maxesstuff\Teamspeak3\Query\Command $cmd, $raw);
    
    /**
     * Checks if the response, provided in $raw is complete
     * @param string $raw
     * @return boolean
     */
    public function isCompleteResponse($raw);
    
    /**
     * Checks if the response, provided in $raw, is a complete event
     * @return boolean
     */
    public function isCompleteEvent($raw);
    
    /**
     * Returns the length of the welcomemessage of the server
     * @return int
     */
    public function getWelcomeMessageLength();
    
    /**
     * Returns if the string in $welcome is a valid WelcomeMessage
     * @param string $welcome
     * @return boolean
     */
    public function isWelcomeMessage($welcome);
    
    /**
     * Builds event-objects from a query-response
     * @param string $raw the query response
     * @return array array of Event
     */
    public function getEventInstances($raw);
}

?>
