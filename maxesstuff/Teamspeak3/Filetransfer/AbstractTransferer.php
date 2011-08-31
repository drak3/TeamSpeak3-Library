<?php

declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\FileTransfer;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 *
 * @author drak3
 */
class AbstractTransferer
{
    
    /**
     * @var \maxessuff\Transmission\TransmissionInterface
     */
    protected $transmission;
    
    public abstract function transfer();
    
    public function sendFull($data, $bytesToSend) {
        while($bytesToSend !== 0) {
            $bytesToSend -= $this->transmission->send($data);
        }
    }

}

?>
