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
namespace devmx\Teamspeak3\Query\Transport\Decorator\Event;
use devmx\Teamspeak3\Query\Transport\TransportInterface;
use devmx\Teamspeak3\Query\Response\CommandResponse;

/**
 *
 * @author drak3
 */
class ResponseEvent extends QueryTransportEvent
{
    
    protected $response;
    
    public function __construct(TransportInterface $transport, CommandResponse $response) {
        parent::__construct($transport);
        $this->setResponse($response);
    }
    
    public function setResponse(CommandResponse $response) {
        $this->response = $response;
    }
    
    public function getResponse() {
        return $this->response;
    }
}

?>
