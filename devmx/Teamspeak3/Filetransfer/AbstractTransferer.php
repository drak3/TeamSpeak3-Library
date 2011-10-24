<?php
declare(encoding="UTF-8");
namespace devmx\Teamspeak3\FileTransfer;

/**
 * The base class for all actions done on the Teamspeak3
 * @author drak3
 */
abstract class AbstractTransferer
{
    
    /**
     * @var \maxessuff\Transmission\TransmissionInterface
     */
    protected abstract $transmission;
    
    /**
     * This function should start the transfer action
     * @return mixed
     */
    public abstract function transfer();
    
    /**
     * Sends given data to the transmission
     * blocks until ALL data is written
     * @param string $data the data to send
     */
    protected function sendFull($data, $bytesToSend) {
        while($bytesToSend !== 0) {
            $bytesToSend -= $this->transmission->send($data);
        }
    }

}

?>
