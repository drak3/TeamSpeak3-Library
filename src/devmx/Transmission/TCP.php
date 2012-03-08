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
 * Implements the TrannsmissionInterface using TCP as protocol to comunicate with the server
 * @author drak3
 */
class TCP implements TransmissionInterface
{
    
    /**
     * Parameter for \stream_set_blocking to set the stream to blocking mode 
     */
    const BLOCKING = 1;
    
    /**
     * Parameter for \stream_set_blocking to set the stream to nonblocking mode 
     */
    const NONBLOCKING = 0;

    /**
     * The host to connect to
     * @var string 
     */
    private $host;
    
    /**
     * The original host given in the constructor (without "tcp://" at the beginning)
     * @var string 
     */
    private $originalHost;

    /**
     * The port to connect to
     * @var int 
     */
    private $port;

    /**
     * The default timeout in seconds
     * @var int 
     */
    protected $defaultTimeoutSec = 5;

    /**
     * The default timeout microseconds
     * @var int
     */
    protected $defaultTimeoutMicro = 0;

    /**
     * The underlying ressource
     * @var ressource
     */
    protected $stream;
    
    /**
     * Indicates if the transmission is connected or not
     * @var type 
     */
    protected $isConnected = false;
    
    /**
     * Max tries we have to send/receive data (-1 means endless)
     * @var int
     */
    protected $maxTries = -1;

    /**
     * Constructor
     * @param string $host the host to connect to
     * @param int $port the port to connect to
     * @param int $timeoutSeconds the seconds to wait at each establish/send/receive action
     * @param int $timeoutMicroSeconds  the microseconds to wait additionaly to the seconds at each establish/send/receive action
     */
    public function __construct($host, $port, $timeoutSeconds = 5, $timeoutMicroSeconds = 0)
    {

        $this->setHost($host);
        $this->setPort($port);


        $this->defaultTimeoutSec = (int) $timeoutSeconds;
        $this->defaultTimeoutMicro = (int) $timeoutMicroSeconds;
    }

    /**
     * Closes the transmission
     */
    public function close()
    {
        $this->closeStream();
        $this->isConnected = FALSE;
    }

    /**
     * Establishes a connection to the setted host/port combination
     * @param int $timeout
     * @param boolean $reEstablish set to true to force a reestablishing of the transmission
     * @throws Exception\EstablishingFailedException
     */
    public function establish($timeout = -1, $reEstablish=false)
    {
        if($this->isEstablished() && !$reEstablish) {
            return;
        }
        
        $errorNumber = 0;
        $errorMessage = '';

        if ($timeout === -1)
        {
            $timeout = $this->defaultTimeoutSec;
        }

        $this->open($this->host, $this->port, $errorNumber, $errorMessage, $timeout);

        if (!$this->stream || $errorNumber !== 0)
        {
            $this->isConnected = FALSE;
            throw new Exception\EstablishingFailedException($this->host, $this->port, $errorNumber, $errorMessage);
        }

        $this->isConnected = true;
    }

    /**
     * Returns the current host (needn't to be the host currently connected to, just the host where next establish() call will connect to)
     * @return string
     */
    public function getHost()
    {
        return $this->originalHost;
    }

    /**
     * Returns the current port (needn't to be the port currently connected to, just the port where next establish() call will connect to
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * If the transmission is established or not
     * @return boolean
     */
    public function isEstablished()
    {
        if ($this->stream == FALSE) return FALSE;
        return $this->isConnected;
    }

    /**
     * Reads $lenght of data from the transmission
     * note that it stops if new data is on the stream or after a line (see stream_get_line())
     * return is trimmed
     * @param int $length
     * @param int $timeoutSec
     * @param int $timeoutMicro
     * @return byte the received data
     */
    public function receiveLine($length = 4096, $timeoutSec = -1, $timeoutMicro = -1)
    {
        if (!$this->isEstablished()) throw new Exception\NotEstablishedException();

        $this->checkTimeOut($timeoutSec, $timeoutMicro);

        $data = $this->getLine( $length );

        return $data;
    }

    /**
     * Returns all data currently on the stream
     * This method is non-blocking 
     * @return string 
     */
    public function getAll()
    {
        if (!$this->isEstablished()) throw new Exception\NotEstablishedException();
        $this->setBlocking( self::NONBLOCKING );
        $crnt = $data = '';
        while ($crnt = $this->getLine(8094))
        {
            $data .= $crnt;
        }
        $this->setBlocking( self::BLOCKING );
        return $data;
    }
    
    /**
     * Sets the max tries a method may take to send or receive the wanted data
     * -1 means endless tries
     * @param int $tries 
     */
    public function setMaxTries($tries) {
        $this->maxTries = $tries;
    }
    
    /**
     * Gets the max tries a method may take to send or receive the wanted data
     * -1 means endless tries
     * @return int
     */
    public function getMaxTries() {
        return $this->maxTries;
    }

    /**
     * Receives data with the given length
     * This method is blocking
     * @param int $length
     * @param int $timeoutSec
     * @param int $timeoutMicro
     * @return string 
     */
    public function receiveData($length, $timeoutSec=-1, $timeoutMicro=-1)
    {
        if (!$this->isEstablished()) throw new Exception\NotEstablishedException;
        $data = '';
        $tries = 0;
        
        $this->checkTimeOut($timeoutSec, $timeoutMicro);
        
        while (strlen($data) < $length)
        {
            $tries++;
            if($this->getMaxTries() > 0 && $tries > $this->getMaxTries()) {
                throw new Exception\MaxTriesExceededException($this->getMaxTries(), $data);
            }
            $data .= $this->getLine($length);
        }
        return $data;
    }

    /**
     * Writes data to the transmission
     * @param byte $data
     * @param int $timeoutSec
     * @param int $timeoutMicro
     * @return int number of written bytes
     */
    public function send($data, $timeoutSec = -1, $timeoutMicro = -1)
    {
        if (!$this->isEstablished()) throw new Exception\NotEstablishedException;
        
        $this->checkTimeOut($timeoutSec, $timeoutMicro);
        
        $bytesToSend = strlen($data);
        
        $tries = 0;
        while ($bytesToSend > 0 && ($tries < $this->getMaxTries() || $this->getMaxTries() < 0))
        {
            $tries++;
            $sentBytes = $this->write($data);
            $bytesToSend -= $sentBytes;
            $data = substr($data, $sentBytes);
        }
        
        if($tries == $this->getMaxTries() && $this->getMaxTries() > 0) {
            throw new Exception\MaxTriesExceededException($this->getMaxTries(), $bytesToSend);
        }
    }
    
    
    /**
     * Clones the transmission 
     */
    public function __clone() {
        if($this->isConnected) {
            $this->establish(-1, true);
        }
    }
    
    /**
     * Sets the host
     * @param string $host
     */
    private function setHost($host)
    {
        $validatedHost = \trim((string) $host);
        if ($validatedHost === '')
        {
            throw new Exception\InvalidHostException($host);
        }
        else
        {
            $this->originalHost = $validatedHost;
            $this->host = "tcp://" . $validatedHost;
        }
    }

    /**
     * Sets a port
     * @param int $port must be between 1 and 65535
     */
    private function setPort($port)
    {
        $port = (int) $port;
        if ($port <= 0 || $port > 65535)
        {
            throw new Exception\InvalidPortException($port);
        }
        else
        {
            $this->port = $port;
        }
    }
    
    /**
     * Returns the underlying stream
     * @return type 
     */
    public function getStream() {
        return $this->stream;
    }
    
    /**
     * 
     * @param type $timeoutSeconds
     * @param type $timeoutMicroseconds 
     */
    protected function checkTimeOut($timeoutSeconds, $timeoutMicroseconds) {
        $timeoutSeconds = (int) $timeoutSeconds;
        $timeoutMicroseconds = (int) $timeoutMicroseconds;

        if ($timeoutMicroseconds < 0)
        {
            $timeoutMicroseconds = $this->defaultTimeoutMicro;
        }

        if ($timeoutSeconds < 0)
        {
            $timeoutSeconds = $this->defaultTimeoutSec;
        }
        $this->setTimeOut($timeoutSeconds, $timeoutMicroseconds);
    }
    
    protected function open($host, $port, &$errno, &$errmsg, $timeout) {
        $this->stream = fsockopen($host, $port, $errno, $errmsg, $timeout);
    }
    
    protected function setTimeOut($seconds, $microseconds) {
        return \stream_set_timeout($this->stream , $seconds , $microseconds);
    }
    
    protected function getLine($length) {
        return \fgets($this->stream, $length);
    }
    
    protected function setBlocking($mode) {
        return \stream_set_blocking($this->stream, $mode);
    }
    
    protected function write($data) {
        return \frwite($this->stream, $data);
    }
    
    protected function closeStream() {
        return \fclose($this->stream);
    }

}

?>
