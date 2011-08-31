<?php
declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\FileTransfer;

/**
 * A download action for the Teamspeak3 ft-Interface
 * @author drak3
 */
class Downloader extends AbstractTransferer
{
    /**
     * The key which is sent to start the action
     * @var string  
     */
    protected $key;
    /**
     * the excepted bytes of the data to read 
     */
    protected $bytesToRead;
    
    /**
     * @param \maxesstuff\Transmission\TransmissionInterface $transmission the transmission on which the download is performed
     * @param string $key the key to identify the ft-Session (normally sent by the Ts3-Query
     * @param int $bytesToRead 
     */
    public function __construct(\maxesstuff\Transmission\TransmissionInterface $transmission, $key, $bytesToRead) {
        $this->transmission = $transmission;
        $this->key = $key;
        $this->bytesToRead = $bytesToRead;
    }
    
    /**
     * Downloads the file specified by the $key
     * @return string the downloaded file 
     */
    public function transfer() {
        if(!$this->transmission->isEstablished())
            $this->transmission->establish();
        $this->sendFull($key);
        return $this->receiveFull($this->bytesToRead);
    }
    
    /**
     * Reads data from the stream until $toRead bytes are received
     * blocks until ALL bytes are read
     * @param int $toRead Number of bytes to read
     * @return string the read data
     */
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
