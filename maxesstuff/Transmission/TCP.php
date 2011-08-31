<?php
declare(encoding="UTF-8");
namespace maxesstuff\Transmission;

/**
 * 
 *
 * @author drak3
 */
class TCP implements TransmissionInterface
{
    const BLOCKING = 1;
    const NONBLOCKING = 0;

    protected $host;
    protected $port;
    protected $defaultTimeoutSec = 5;
    protected $defaultTimeoutMicro = 0;
    protected $stream;

    /**
     *
     * @param string $host the host to connect to
     * @param int $port the port to connect to
     * @param int $timeoutSeconds the seconds to wait at each establish/send/receive action
     * @param int $timeoutMicroSeconds  the seconds to wait at each establish/send/receive action
     */
    public function __construct( $host, $port, $timeoutSeconds=5,
                                 $timeoutMicroSeconds=0 )
    {

        $this->setHost( $host );
        $this->setPort( $port );


        $this->defaultTimeoutSec = (int) $timeoutSeconds;
        $this->defaultTimeoutMicro = (int) $timeoutMicroSeconds;

    }

    /**
     * closes the transmission
     * @return void
     */
    public function close()
    {
        fclose( $this->stream );
        $this->isConnected = FALSE;

    }

    /**
     * Establishes a connection to the setted host/port combination
     * @param int $timeout
     * @return void
     * @throws \RuntimeException
     */
    public function establish( $timeout = -1 )
    {
        $errorNumber = 0;
        $errorMessage = '';

        if ( $timeout === -1 )
        {
            $timeout = $this->defaultTimeoutSec;
        }

        $this->stream = fsockopen( $this->host, $this->port, $errorNumber, $errorMessage, $timeout );

        if ( !$this->stream || $errorNumber !== 0 )
        {
            $this->isConnected = FALSE;
            throw new \RuntimeException( "Establishing failed with code " . $errorNumber . "and message " . $errorMessage );
        }

        $this->isConnected = true;

    }

    /**
     * Returns the current host (needn't to be the port currently connected to, just the port where next establish() call will connect to
     * @return string
     */
    public function getHost()
    {
        return $this->host;

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
    public function receiveLine( $length = 4096, $lineEnd ="\n",
                                 $timeoutSec = -1, $timeoutMicro=-1 )
    {

        $timeoutSec = (int) $timeoutSec;
        $timeoutMicro = (int) $timeoutMicro;

        if ( $timeoutMicro < 0 )
        {
            $timoutMicro = $this->defaultTimeoutMicro;
        }

        if ( $timeoutSec < 0 )
        {
            $timeoutSec = $this->defaultTimeoutSec;
        }

        \stream_set_timeout( $this->stream, $timeoutSec, $timeoutMicro );

        $data = \stream_get_line( $this->stream, $length, $lineEnd);

        return $data;

    }

    public function getAll() {
        \stream_set_blocking($this->stream, self::NONBLOCKING);
        $crnt = $data = '';
        while($crnt = \trim(\fgets($this->stream))) {
            $data .= $crnt;
        }
        \stream_set_blocking($this->stream, self::BLOCKING);
        return $data;
    }

    public function  receiveData( $lenght = 4096 )
    {
        $data = '';
        while(strlen($data) < $lenght) {
            $data .= \fgets($this->stream);
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
    public function send( $data, $timeoutSec = -1, $timeoutMicro=-1 )
    {
        $timeoutSec = (int) $timeoutSec;
        $timeoutMicro = (int) $timeoutMicro;

        if ( $timeoutMicro < 0 )
        {
            $timoutMicro = $this->defaultTimeoutMicro;
        }

        if ( $timeoutSec < 0 )
        {
            $timeoutSec = $this->defaultTimeoutSec;
        }
        \stream_set_timeout( $this->stream, $timeoutSec, $timeoutMicro );
        return \fwrite( $this->stream, $data );

    }

    /**
     * Sets the host
     * @param string $host
     */
    private function setHost( $host )
    {
        $host = \trim( (string) $host );
        if ( $host === '' )
        {
            throw new \InvalidArgumentException( "Invalid Host " . $host );
        }
        else
        {
            $this->host = "tcp://" . $host;
        }

    }

    /**
     * Sets a port
     * @param int $port must be between 1 and 65535
     */
    private function setPort( $port )
    {
        $port = (int) $port;
        if ( $port <= 0 || $port > 65535 )
        {
            throw new \InvalidArgumentException( "Invalid Port " . $port );
        }
        else
        {
            $this->port = $port;
        }

    }
}

?>
