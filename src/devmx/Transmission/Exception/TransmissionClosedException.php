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
 * Exception is thrown when transmission was closed by foreign host
 * @author drak3
 */
class TransmissionClosedException extends RuntimeException
{   
    /**
     * The data occured before the connection was closed
     * @var string
     */
    protected $data;
    
    /**
     * Constructor
     * @param string $message
     * @param string $data
     * @param int $code
     * @param Exception $previous 
     */
    public function __construct($message, $data, $code=0, $previous=null) {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }
    
    /**
     * Returns the data occured before the exception was thrown
     * @return string
     */
    public function getData() {
        return $this->data;
    }
}

?>
