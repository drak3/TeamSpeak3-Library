<?php
declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\FileTransfer;

/**
 * 
 *
 * @author drak3
 */
class Uploader extends AbstractTransferer
{
   /**
    *
    * @var \maxesstuff\Transmission\TransmissionInterface
    */ 
   protected $transmission;
   protected $key;
   protected $data;

   public function __construct(\maxesstuff\Transmission\TransmissionInterface $transmission, $key, $data) {
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
