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

namespace devmx\Teamspeak3\Query\Transport\Common;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\Response\HelpResponse;

/**
 *
 * @author drak3
 */
class HelpAwareResponseHandler extends ResponseHandler
{
    
    protected function parseResponse(Command $cmd, $error, $data='') {
        if($cmd->getName() === 'help') {
            return $this->parseHelpResponse($cmd, $error, $data);
        } else {
            return parent::parseResponse($cmd, $error, $data);
        }
    }
    
    /**
     * This method parses a response issued by a help command
     * It simply takes the data as helpMessage
     * @param Command $cmd
     * @param string $error
     * @param string $data
     * @return \devmx\Teamspeak3\Query\Response\HelpResponse 
     */
    protected function parseHelpResponse(Command $cmd, $error, $data) {
        $parsedError = $this->parseData($error);
        $errorID = $parsedError[0]['id'];
        $errorMessage = $parsedError[0]['msg'];
        return new HelpResponse($cmd, $data, $errorID, $errorMessage, $parsedError[0]);
    }
}

?>
