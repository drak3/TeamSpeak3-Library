<?php
declare(encoding="UTF-8");
namespace devmx\Teamspeak3\FileTransfer;

/**
 * 
 *
 * @author drak3
 */
class Uploader extends AbstractTransferer
{
   /**
    *
    * @var \devmx\Transmission\TransmissionInterface
    */ 
   protected $transmission;
   protected $key;
   protected $data;

   public function __construct(\devmx\Transmission\TransmissionInterface $transmission, $key, $data) {
       $this->transmission = $transmission;
       $this->key = $key;
   }
   
   public function transfer() {
       $bytesToSend = strlen($this->data); 
       $this->transmission->establish();
       $this->sendFull($key, strlen($key));
       $this->sendFull($data, $bytesToSend);
       $this->transmission->close();
   }
   
    
}

?>
