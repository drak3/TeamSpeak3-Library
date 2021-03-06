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
namespace devmx\Teamspeak3\Query\Transport;
use devmx\Teamspeak3\Query\Command;

/**
 * Base Interface of all ResponseHandlers
 * A responsehandler handles and parses all data coming from the query
 * @author drak3
 */
interface ResponseHandlerInterface
{

    /**
     * Builds a CommandResponse from the query-returned data
     * @param Command $cmd
     * @param string $raw
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function getResponseInstance(Command $cmd, $raw);

    /**
     * Checks if the response, provided in $raw is complete
     * @param string $raw
     * @return boolean
     */
    public function isCompleteResponse($raw);

    /**
     * Checks if the response, provided in $raw, is a complete event
     * @param string $raw the raw query response
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
     * @param string $ident the first line returned by the server
     * @return boolean
     */
    public function isValidQueryIdentifyer($ident);

    /**
     * Builds event-objects from a query-response
     * @param string $raw the query response
     * @return array array of Event
     */
    public function getEventInstances($raw);
    
}

?>
