<?php

declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\FileTransfer;

/**
 * 
 *
 * @author drak3
 */
class Downloader extends AbstractTransferer
{
    protected $key;
    protected $bytesToRead;
    
    public function __construct(\maxesstuff\Transmission\TransmissionInterface $transmission, $key, $bytesToRead) {
        $this->transmission = $transmission;
        $this->key = $key;
        $this->bytesToRead = $bytesToRead;
    }
    
    public function transfer() {
        $this->transmission->establish();
        $this->sendFull($key,strlen($key));
        return $this->receiveFull($this->bytesToRead);
    }
    
    private function receiveFull($toRead) {
        $result = $cur = '';
        while($toRead > 0) {
            $cur = $this->transmission->receiveData($toRead);
            $toRead -= strlen($cur);
            $result .= $cur;
        }
        return $result;
    }
    
    
    
}

?>
