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
     */
    public function establish();
    
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
     */
    public function send($data);

    /**
     * Waits until a line end or data of the given length is received and returns the data (blocking)
     * @param int $length if $length bytes are received, the data is returned
     * @return string the received data
     */
    public function receiveLine($length = 4096);

    /**
     * Returns all data currently on the stream (nonblocking)
     * @return string the data currently on the stream
     */
    public function getAll();

    /**
     * Waits until given datalength is sent and returns it (blocking)
     * @param $length the length of the data to wait for
     * @return string
     */
    public function receiveData($lenght);

    /**
     * Closes the transmission 
     */
    public function close();
}

?>
