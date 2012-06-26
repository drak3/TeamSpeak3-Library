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
 * Thrown if the hist given to a transmission is invalid
 * @author drak3
 */
class InvalidHostException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * The invalid host
     * @var string
     */
    private $host;
    
    /**
     * Constructor
     * @param string $host the invalid host 
     */
    public function __construct($host) {
        parent::__construct(sprintf('Invalid host "%s". Host must valid ip or domain name', $host));
        $this->host = $host;
    }
    
    /**
     * Returns the invalid host
     * @return string 
     */
    public function getHost() {
        return $this->host;
    }
}

?>
