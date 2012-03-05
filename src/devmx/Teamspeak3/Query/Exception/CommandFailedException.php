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
 *
 * @author drak3
 */
class CommandFailedException extends RuntimeException
{
    private $response;
    
    public function __construct(CommandResponse $response) {
        parent::__construct($this->buildMessage($response));
        $this->response = $response;
    }
    
    public function getRespose() {
        return $this->response;
    }
    
    private function buildMessage(CommandResponse $response) {
        $message = sprintf('Command "%s" caused error with message "%s" and id %d.', $response->getCommand()->getName(), $response->getErrorMessage(), $response->getErrorID());
        if($response->hasErrorValue( 'extra_message')) {
            $message .= sprintf(' (Extra message: "%s")', $response->getErrorValue('extra_message'));
        }
        return $message;
    }
}

?>
