<?php
declare(encoding="UTF-8");
namespace maxesstuff\Transmission;
/**
 *
 * @author drak3
 */
interface TransmissionInterface
{
    public function establish();

    public function getPort();
    public function getHost();

    public function isEstablished();

    public function send( $data );
        
    /**
     * waits until a line end and returns the data (blocking)
     */
    public function receiveLine( $length=4096 , $lineEnd="\n");

    /**
     * Returns all data currently on the stream (nonblocking)
     */
    public function getAll();

    /**
     * waits until given datalength is sent and returns data
     */
    public function receiveData($lenght=4096);
    
    public function close();
}

?>
