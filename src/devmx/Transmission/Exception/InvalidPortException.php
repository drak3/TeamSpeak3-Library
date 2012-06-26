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
namespace devmx\Transmission\Exception;

/**
 * This class is thrown, when an invalid port is given to the transmission
 * @author drak3
 */
class InvalidPortException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * The invalid port
     * @var mixed
     */
    private $port;
    
    /**
     * Constructor
     * @param mixed $port 
     */
    public function __construct($port) {
        parent::__construct(sprintf('Port %s is invalid, valid port must be between 0 and 65535', $port));
        $this->port = $port;
    }
    
    /**
     * Returns the invalid port
     * @return int
     */
    public function getPort() {
        return $this->port;
    }
}

?>
