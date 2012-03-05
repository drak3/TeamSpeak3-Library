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
namespace devmx\Teamspeak3\FileTransfer;

/**
 * 
 *
 * @author drak3
 */
class Uploader
{

    /**
     * The transmission on which the transfer is executed
     * @var \devmx\Transmission\TransmissionInterface
     */
    protected $transmission;
    
    /**
     * the key identifying this upload 
     * @var string
     */
    protected $key;
    
    /**
     * the data to upload
     * @var string
     */
    protected $data;
    
    /**
     * @param \devmx\Transmission\TransmissionInterface $transmission the transmission on which the upload should be performed (default ft-port is 30033)
     * @param string $key the key which identifies the ressource to upload (normally sent by the Ts3-Query when invoking ftinitupload command successfully)
     * @param string $data the data to load up
     */
    public function __construct(\devmx\Transmission\TransmissionInterface $transmission, $key, $data)
    {
        $this->transmission = $transmission;
        $this->key = $key;
        $this->data = $data;
    }
    
    /**
     * Uploads the specified data 
     */
    public function upload()
    {
        $this->transmission->establish();
        $this->transmission->send($this->key);
        $this->transmission->send($this->data);
        $this->transmission->close();
    }

}

?>
