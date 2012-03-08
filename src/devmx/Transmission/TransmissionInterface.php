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
namespace devmx\Transmission;

/**
 * This interface provides basic methods to communicate with a (remote) server
 * @author drak3
 */
interface TransmissionInterface
{

    /**
     * Establishs the transmission 
     * @param float $timeout the timeout defines in seconds how long the transmission should try to establish itself -1 means that the method invoker has no special requirements to the timeout
     *   the real timeout MUST be greater then $timout, it SHOULD take at least the first two decimal digits as microsecond timeout
     * @throws \devmx\Transmission\Exception\EstablishingFailedException
     */
    public function establish($timeout=-1);
    
    /**
     * Rertuns the hostname of the other end of the Transmission
     * @return string 
     */
    public function getHost();

    /**
     * Returns the port of the other end of the Transmission
     * @return string  
     */
    public function getPort();

    
    /**
     * Returns wether the Transmission is established or not
     * @return boolean 
     */
    public function isEstablished();
    
    /**
     * Sends the given data to the host
     * @param string $data the data to send
     * @param float $timeout the time in seconds after which the sending action is considered as broken if $timeout is set to -1 it means that the method invoker has no special requirements to the timeout
     *   the real timeout MUST be greater or equal as $timeout, at least the first two decimal digits SHOULD be used to define the timeout
     * @throws \devmx\Transmission\Exception\TimeoutException
     */
    public function send($data, $timeout=-1);

    /**
     * Waits until a line end is received and returns the data (blocking)
     * Caution: even if the timeout parameter is set to a value greater 0, 
     *   the time this method takes could be indefinite long, because it doesn't return until a lineend is reached, or there is a gap of at least $timeout in the transmission
     * @param float $timeout the time in seconds after which the receiving action is stopped when no new data was received if $timeout is set to -1 it means that the method invoker has no special requirements to the timeout
     *        the real timeout MUST be greater or equal as $timeout, at least the first two decimal digits SHOULD be used to define the timeout
     * @return string the received data
     * @throws \devmx\Transmission\Exception\TimeoutException
     */
    public function receiveLine($timeout=-1);

    /**
     * Returns all data currently on the stream (nonblocking)
     * @return string the data currently on the stream
     */
    public function checkForData();

    /**
     * Waits until given datalength is sent and returns it (blocking)
     * Caution: the time this method takes could be longer than the given timeout
     * @param int $length the length of the data to wait for
     * @param float $timeout the time in seconds after which the receiving action is stopped when no new data was received if $timeout is set to -1 it means that the method invoker has no special requirements to the timeout
     *        the real timeout MUST be greater or equal as $timeout, at least the first two decimal digits SHOULD be used to define the timeout
     * @return string the received data
     * @throws \devmx\Transmsission\Exception\TimeoutException
     */
    public function receiveData($lenght, $timeout=-1);

    /**
     * Closes the transmission 
     */
    public function close();
}

?>
