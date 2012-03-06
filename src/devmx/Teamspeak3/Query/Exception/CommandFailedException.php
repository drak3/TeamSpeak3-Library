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
namespace devmx\Teamspeak3\Query\Exception;
use devmx\Teamspeak3\Query\CommandResponse;

/**
 * The exception thrown when an command sent to the Query resulted in an error
 * This is normaly detected by checking if the errorID is equal to 0 (= no error occured)
 * @author drak3
 */
class CommandFailedException extends RuntimeException
{
    /**
     * The response indicating the failure
     * @var CommandResponse
     */
    private $response;
    
    /**
     * Constructor
     * @param CommandResponse $response the response caused by the failed command
     */
    public function __construct(CommandResponse $response) {
        parent::__construct($this->buildMessage($response));
        $this->response = $response;
    }
    
    /**
     * Returns the response caused by the failed command
     * This response contains (among other things) the errorID and the errorMessage
     * @return CommandResponse
     */
    public function getRespose() {
        return $this->response;
    }
    
    /**
     * Builds the Exceptionmessage, which does contain information about the commandname, the error id and message and, if present, the extra_message or failed_permid
     * @param CommandResponse $response
     * @return string
     */
    private function buildMessage(CommandResponse $response) {
        $message = sprintf('Command "%s" caused error with message "%s" and id %d.', $response->getCommand()->getName(), $response->getErrorMessage(), $response->getErrorID());
        if($response->hasErrorValue('extra_message')) {
            $message .= sprintf("\n".'Extra message: "%s"', $response->getErrorValue('extra_message'));
        }
        if($response->hasErrorValue('failed_permid')) {
            $message .= sprintf("\n".'Not enough rights, failed permission ID: %d', $response->getErrorValue('failed_permid'));
        }
        return $message;
    }
}

?>
