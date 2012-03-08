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
 * This exception is thrown, when the maximum tries to do sth. on the query exceeded
 * @author drak3
 */
class MaxTriesExceededException extends \RuntimeException implements ExceptionInterface
{   
    /**
     * The max tries
     * @var int 
     */
    private $tries;
    
    /**
     * The data processed before the tries exceeded
     * @var string
     */
    private $data;
    
    /**
     * Constructor
     * @param int $tries
     * @param string $data 
     */
    public function __construct($tries, $data) {
        parent::__construct(sprintf('Maximum of tries (%d) exceeded, this could be because of to much data to transfer or a bad connection to the host', $tries));
        $this->tries = $tries;
        $this->data = $data;
    }
    
    /**
     * Returns the max tries
     * @return int 
     */
    public function getMaximumTries() {
        return $this->tries;
    }
    
    /**
     * Returns the incomplete data
     * @return string
     */
    public function getIncompleteData() {
        return $this->data;
    }
}

?>
