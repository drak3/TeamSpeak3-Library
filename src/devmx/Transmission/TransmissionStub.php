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
 * Implementation of the Transmissioninterface for unittesting
 * @author drak3
 */
class TransmissionStub implements \devmx\Transmission\TransmissionInterface
{
    /**
     * If the the transmission is established
     * @var boolean 
     */
    protected $isEstablished = false;
    
    /**
     * The host we are "connected" to
     * @var string
     */
    protected $host;
    
    /**
     * The port we are "connected" to
     * @var int
     */
    protected $port;
    
    /**
     * The data sent to the host
     * @var string
     */
    protected $sentData = '';
    
    /**
     * The data currently lying on the stream
     * @var string
     */
    protected $toReceive = '';
    
    /**
     * The data received from the stream
     * @var string 
     */
    protected $received = '';
    
    /**
     * Indicates if blocking methods are allowed
     * @var boolean
     */
    protected $errorOnDelay = false;
    
    /**
     * Indicates if the underlying stream timed out
     * @var boolean
     */
    protected $isTimedOut = false;
    
    /**
     * Number of clones made
     * @var int
     */
    protected static $cloned = 0;
    
    /**
     * Constructor
     * @param string $host
     * @param int $port 
     */
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }
    
    /**
     * Establishes the transmission
     * @param int $timeout is ignored 
     */
    public function establish($timeout=-1) {
        $this->isEstablished = true;
    }
    
    /**
     * Returns the hosts port
     * @return int 
     */
    public function getPort() {
        return $this->port;
    }
    
    /**
     * Returns the hostname
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Returns true if the Transmission is established
     * @return boolean
     */
    public function isEstablished() {
        return $this->isEstablished;
    }
    
    /**
     * Sends data to the stream
     * sent data could be get via getSentData
     * @param string $data
     * @param int $timeout is ignored
     * @return boolean
     * @throws Exception\NotEstablishedException 
     */
    public function send($data, $timeout=-1) {
        if(!$this->isEstablished()) {
            throw new Exception\NotEstablishedException;
        }
        $this->sentData .= $data;
        return strlen($data);
    }
    
    /**
     * Return the data sent to the Transmission
     * @return string
     */
    public function getSentData() {
        return $this->sentData;
    }
    
    /**
     * Sets the data sent to the Transmission
     * @param string $data 
     */
    public function setSentData($data) {
        $this->sentData = $data;
    }
    
    /**
     * If blocking methods are allowed
     * @param boolean $error 
     */
    public function errorOnDelay($error) {
        $this->errorOnDelay = $error;
    }
    
    /**
     * Lets the stream timeout if timeout is set to true
     * @param boolean $timeout 
     */
    public function timeout($timeout=true) {
        $this->isTimedOut = $timeout;
    }

    /**
     * waits until a line end and returns the data (blocking)
     * @param float $timeout is ignored
     */
    public function receiveLine($timeout=-1) {
        if($this->isTimedOut) {
            throw new Exception\TimeoutException('receiveLine timed out', $timeout, '');
        }
        if(!$this->isEstablished()) {
            throw new Exception\NotEstablishedException();
        }
        if($this->errorOnDelay) {
            throw new Exception\LogicException('This function causes delay, not allowed');
        }
        $lines = \explode("\n",$this->toReceive);
        if(!isset($lines[0])) {
            throw new Exception\LogicException('Cannot receive line, not enough data');
        }
        $ret = $lines[0]."\n";
        $count = 0;
        $this->toReceive = \preg_replace('/'.\preg_quote($ret,'/').'/m','', $this->toReceive, 1, $count);
        if($count !== 1) {
            throw new Exception\LogicException('string is not found, BUG');
        }
        $this->received .= $ret;
        return $ret;
    }
    
    /**
     * Sets the data which could be received
     * @param string $toReceive 
     */
    public function setToReceive($toReceive) {
        $this->toReceive = $toReceive;
    }
    
    /**
     * Gets the data which could be received
     * @return string
     */
    public function getToReceive() {
        return $this->toReceive;
    }
    
    /**
     * Adds data to the receivable data
     * @param string $toAdd 
     */
    public function addToReceive($toAdd) {
        $this->toReceive .= $toAdd;
    }
    
    /**
     * Returns the data wich was received
     */
    public function getReceived() {
        return $this->received;
    }
    
    /**
     * Sets the data which was received
     * @param string $r 
     */
    public function setReceived($r) {
        $this->received = $r;
    }

    /**
     * Returns all data currently on the stream (nonblocking)
     */
    public function checkForData() {
        if(!$this->isEstablished()) {
            throw new Exception\NotEstablishedException();
        }
        $this->received .= $this->toReceive;
        $ret = $this->toReceive;
        $this->toReceive = '';
        return $ret;
    }

    /**
     * waits until given datalength is sent and returns data
     * @param int $length
     * @param float $timeout is ignored
     */
    public function receiveData($length, $timeout=-1) {
        if($this->isTimedOut) {
            throw new Exception\TimeoutException('receiveLine timed out', $timeout, '');
        }
        if($this->errorOnDelay) {
            throw new Exception\LogicException('This function causes delay, not allowed');
        }
        if(!$this->isEstablished()) {
            throw new Exception\NotEstablishedException;
        }
        if(strlen($this->toReceive) < $length) {
            throw new Exception\NotEstablishedException("Cannot receive $length bytes");
        }
        $ret = substr($this->toReceive,0,$length);
        $this->toReceive = substr($this->toReceive, $length);
        $this->received .= $ret;
        if(strlen($ret) < $length) {
            throw new Exception\LogicException('BUG');
        }
        return $ret;
    }

    /**
     * Closes the stream 
     */
    public function close() {
        $this->isEstablished = FALSE;
    }
    
    /**
     * Clones the Transmission 
     */
    public function __clone() {
        self::$cloned++;
    }
    
    /**
     * Returns how many clones were made
     * @return int 
     */
    public static function cloned() {
        return self::$cloned;
    }
    
}

?>
