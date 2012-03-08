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
 * This exception is throw when the establishing process was not successful
 * @author drak3
 */
class EstablishingFailedException extends \RuntimeException implements ExceptionInterface
{
    /**
     * The host we tried to connect to
     * @var string
     */
    private $host;
    
    /**
     * The port we tried to connect to
     * @var int
     */
    private $port;
    
    /**
     * The id of the error
     * @var int
     */
    private $errno;
    
    /**
     * The message of the error
     * @var string
     */
    private $errmsg;
    
    /**
     * Constructor
     * @param string $host The host tried to connect to
     * @param int $port The port tried to connect to
     * @param int $errno The id of the error
     * @param string $errmsg The message of the error
     */
    public function __construct($host, $port, $errno, $errmsg) {
        parent::__construct(sprintf('Cannot establish connection to %s:%s: Error %d with message "%s"', $host, $port, $errno, $errmsg), $errno );
        $this->host = $host;
        $this->port = $port;
        $this->errno = $errno;
        $this->errmsg = $errmsg;
    }
    
    /**
     * Returns the host where the connectionattempt where made
     * @return string 
     */
    public function getHost() {
        return $this->host;
    }
    
    /**
     * Returns the port
     * @return int 
     */
    public function getPort() {
        return $this->port;
    }
    
    /**
     * Returns the error number
     * @return int
     */
    public function getErrorNumber() {
        return $this->errno;
    }
    
    /**
     * Returns the error message
     * @return string
     */
    public function getErrorMessage() {
        return $this->errmsg;
    }
}

?>
