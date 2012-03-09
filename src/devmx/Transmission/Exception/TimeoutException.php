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
 * This exception is thrown if the timeout exceeded
 * @author drak3
 */
class TimeoutException extends \RuntimeException implements ExceptionInterface
{
    
    /**
     * The timeout that exceeded
     * @var float
     */
    protected $timeout;
    
    /**
     * The data which was successfully proceeded before the timeout occured
     * @var string 
     */
    protected $data;
    
    /**
     * Constructor
     * @param string $message the Exception message
     * @param float $timeout the timeout which was exceeded
     * @param string $data the data which was successfully proceeded before the timeout occured
     * @param int $code
     * @param \Exception $previous 
     */
    public function __construct($message, $timeout, $data, $code=0, $previous=null) {
        parent::__construct($message, $code, $previous);
        $this->timeout = $timeout;
        $this->data = $data;
    }
    
    /**
     * Returns the timeout that exceeded
     * @return float
     */
    public function getTimeout() {
        return $this->timeout;
    }
    
    /**
     *  Returns the data which was successfully proceeded before the timeout occured
     *  @return string
     */
    public function getData() {
        return $this->data;
    }
}

?>
