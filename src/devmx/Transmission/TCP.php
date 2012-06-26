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
     * @var float
     */
    protected $defaultTimeout;

    /**
     * The underlying ressource
     * @var ressource
     */
    protected $stream;
    
    /**
     * Indicates if the transmission was established or not, doesn't say anything about the current state
     * @var boolean
     */
    protected $wasEstablished = false;
    

    /**
     * Constructor
     * @param string $host the host to connect to
     * @param int $port the port to connect to
     * @param float $timeout the default timeout in seconds
     */
    public function __construct($host, $port, $timeout=1)
    {
        $this->setHost($host);
        $this->setPort($port);
        $this->defaultTimeout = $timeout;
    }

    /**
     * Closes the transmission
     */
    public function close()
    {
        $this->closeStream();
        $this->wasEstablished = false;
    }

    /**
     * Establishes a connection to the setted host/port combination
     * @param int $timeout the timeout is rounded up to an int, if -1 is given as $timeout, the default is used
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

        $timeout = $this->getTimeout($timeout, false);

        $this->open($this->host, $this->port, $errorNumber, $errorMessage, $timeout['seconds']);

        if (!$this->stream || $errorNumber !== 0)
        {
            $this->wasEstablished = false;
            throw new Exception\EstablishingFailedException($this->host, $this->port, $errorNumber, $errorMessage);
        }
        $this->wasEstablished = true;
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
        if(!$this->wasEstablished || !$this->stream) {
            return false;
        }
        if($this->hasEof()) {
            // an "End of File" on the stream indicates that the connection was closed by the remote host/was lost
            return false;
        }
        return true;
    }

    /**
     * Waits until a line end is received and returns the data (blocking)
     * Caution: even if the timeout parameter is set to a value greater 0, 
     *   the time this method takes could be indefinite long, because it doesn't return until a lineend is reached, or there is a gap of at least $timeout in the transmission
     * @param float $timeout the time in seconds after which the receiving action is stopped when no new data was received if $timeout is set to -1 the default timeout is used
     * @return string the received data
     * @throws \devmx\Transmission\Exception\TimeoutException
     */
    public function receiveLine($timeout=-1)
    {
        $this->checkConnection();
        $this->requiresTimeout($timeout);
        $data = '';
        $current = '';
        
        while(!isset($data[strlen($data)-1]) || $data[strlen($data)-1] !== "\n") {
            $current = $this->getLine(8192);
            if(!$current) {
                $this->handleTimeout($timeout, $data);
            }
            $data .= $current;
        }
        return $data;
    }

    /**
     * Returns all data currently on the stream
     * This method is non-blocking 
     * @return string 
     */
    public function checkForData()
    {
        $this->checkConnection();
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
     * Waits until given datalength is sent and returns it (blocking)
     * Caution: the time this method takes could be longer than
     * @param int $length the length of the data to wait for
     * @param float $timeout the time in seconds after which the receiving action is stopped when no new data was received if $timeout is set to -1 it means that the default timeout is used
     *        the timeout is rounded up to two decimal digets
     * @return string
     */
    public function receiveData($length, $timeout=-1)
    {
        $this->checkConnection();
        $this->requiresTimeout($timeout);
        $data = '';
        $current = '';
        $toReceive = $length;
        $length -= 1; //fixes unexpected timeouts
        
        while (strlen($data) < $length)
        {
            $current = $this->getLine($toReceive);
            if(!$current) {
                $this->handleTimeout($timeout, $data);
            }
            $data .= $current;
            $toReceive -= strlen($current);
        }
        return $data;
    }

    /**
     * Sends the given data to the host
     * @param string $data the data to send
     * @param float $timeout the time in seconds after which the sending action is considered as broken if $timeout is set to -1 the default timeout is used 
     *   the timeout is rounded up to 2 decimal digits
     * @throws \devmx\Transmission\Exception\TimeoutException
     */
    public function send($data, $timeout=-1)
    {
        $this->checkConnection();
        $this->requiresTimeout($timeout);
        $bytesToSend = strlen($data);
        
        while ($bytesToSend > 0)
        {
            $sentBytes = $this->write($data);
            if($sentBytes === 0) {
                $this->handleTimeout($timeout, $data);
            }
            $bytesToSend -= $sentBytes;
            $data = substr($data, $sentBytes);
        }
    }
    
    
    /**
     * Clones the transmission 
     */
    public function __clone() {
        if($this->wasEstablished) {
            $this->establish(-1, true);
        }
    }
    
    /**
     * Sets the host
     * The hostname is validated
     * @param string $host
     */
    private function setHost($host)
    {
        if(\filter_var($host, FILTER_VALIDATE_IP) !== false) {
            $this->host = $host;
        }
        else {
            if($this->isValidDomainName( $host )) {
                $this->host = $host;
            }
            else {
                throw new Exception\InvalidHostException($host);
            }
        }
        $this->originalHost = $host;
        $this->host = 'tcp://'.$this->host;
    }
    
    /**
     * Validates the given domain name
     * Taken from http://stackoverflow.com/a/4694816
     * @param string $name
     * @return boolean 
     */
    protected function isValidDomainName($name) {
        $pieces = explode(".",$name);
        foreach($pieces as $piece) {
            if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $piece) || preg_match('/-$/', $piece) ) {
                return false;
            }
        }
        return true;
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
     * @return ressource 
     */
    public function getStream() {
        return $this->stream;
    }
    
    /**
     * Returns the default timeout, used when -1 is supplied as timeout argument
     * @return float
     */
    public function getDefaultTimeout() {
        return $this->defaultTimeout;
    }
    
    /**
     * Sets the default timeout which is used when -1 is supplied as timeout argument
     * @param float $timeout 
     */
    public function setDefaultTimeout($timeout) {
        $this->defaultTimeout = $timeout;
    }
    
    /**
     * Converts a given timeout into an array whith information about seconds an microseconds
     * @param float $timeout the timeout to parse if its -1 the default timeout is used
     * @param boolean $microseconds if microseconds should be parsed, if set to false, the timeout will be rounden up to seconds
     * @return array
     */
    protected function getTimeout($timeout, $microseconds=true) {
        if($timeout === -1) {
            $timeout = $this->defaultTimeout;
        }
        $ret = array();
        if($microseconds) {
            $timeout = round($timeout, 2);
            $ret['seconds'] = intval($timeout);
            //micro = (total-seconds) * 10^5 (0.1 seconds are 100000 (=10E5) microseconds)
            $ret['microseconds'] = intval(strval(($timeout-$ret['seconds'])*10E5)); //nasty hack with strval, see http://de3.php.net/manual/en/function.intval.php#86590
        } else {
            $ret['seconds'] = ceil($timeout);
        }
        return $ret;
    }
    
    /**
     * Sets the given timeout
     * @param float $timeout 
     */
    protected function requiresTimeout($timeout) {
        $timeout = $this->getTimeout($timeout);
        $this->setTimeout($timeout['seconds'], $timeout['microseconds']);
    }
    
    /**
     * Checks the connection
     * @throws Exception\LogicException when transmission was never established
     * @throws Exception\RuntimeException when transmission was established, but was closed by foreign host
     */
    protected function checkConnection() {
        if (!$this->wasEstablished) {
            throw new Exception\LogicException('Transmission has to be established before any action could be taken on it');
        }
        if(!$this->isEstablished()) {
            throw new Exception\TransmissionClosedException(sprintf('Transmission to %s:%s was closed by the remote host.', $this->getHost(), $this->getPort()), '');
        }
    }
    
    /**
     * Handles a timeout occured on the stream
     * @param float $timeout the timeout that exceeded
     * @param string $data the data processed so far
     * @throws Exception\RuntimeException if connection was closed by foreign host
     * @throws Exception\TimeoutException if connection timed just out, but was not closed
     */
    protected function handleTimeout($timeout, $data) {
        if(!$this->isEstablished()) {
            throw new Exception\TransmissionClosedException(sprintf("Connection to %s:%s was closed by foreign host.", $this->getHost(), $this->getPort()), $data);
        }
        else {
            if($timeout === -1) {
                $timeout = $this->getDefaultTimeout();
            }
            $msg = sprintf("Connection to %s:%s timed out after %s seconds.", $this->getHost(), $this->getPort(), $timeout);
            throw new Exception\TimeoutException($msg, $timeout, $data);
        }
    }
    
    /**
     * Opens an connection to the given host on the given port
     * (wrapper for fsockopen)
     * @param string $host the host to connect to
     * @param int $port the port to connect to
     * @param int $errno will hold the errornumber if an error occured
     * @param string $errmsg will hold the errormessage if an error occured
     * @param int $timeout the timeout in seconds 
     */
    protected function open($host, $port, &$errno, &$errmsg, $timeout) {
        $this->stream = \fsockopen($host, $port, $errno, $errmsg, $timeout);
    }
    
    /**
     * Sets the timeout for the underlying stream
     * (wrapper for stream_set_timeout)
     * @param int $seconds
     * @param int $microseconds 
     */
    protected function setTimeout($seconds, $microseconds) {
        return \stream_set_timeout($this->stream , $seconds , $microseconds);
    }
    
    /**
     * Gets a line from the stream
     * (wrapper for fgets)
     * @param int $length the maximum length
     * @return string
     */
    protected function getLine($length) {
        return \fgets($this->stream, $length);
    }
    
    /**
     * Sets the blocking mode of the stream
     * (wrapper for stream_set_blocking)
     * @param int $mode 
     */
    protected function setBlocking($mode) {
        return \stream_set_blocking($this->stream, $mode);
    }
    
    /**
     * Writes the data to the stream
     * (wrapper for fwrite)
     * @param string $data
     * @return int bytes written
     */
    protected function write($data) {
        return \fwrite($this->stream, $data);
    }
    
    /**
     * Closes the stream
     * (wrapper for fclose)
     */
    protected function closeStream() {
        return \fclose($this->stream);
    }
    
    /**
     * Checks if the stream received the EOF signal
     * (wrapper for feof)
     * @return boolean
     */
    protected function hasEof() {
        return \feof($this->stream);
    }
    
}

?>
