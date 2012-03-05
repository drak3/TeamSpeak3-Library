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
 * A download action for the Teamspeak3 filetransfer-Interface
 * @author drak3
 */
class Downloader
{

    /**
     * The key which is sent to start the action
     * @var string  
     */
    protected $key;

    /**
     * The excepted bytes of the data to read
     * @var int
     */
    protected $bytesToRead;

    /**
     * @param \devmx\Transmission\TransmissionInterface $transmission the transmission on which the download is performed
     * @param string $key the key to identify the filetransfer-Session (normally sent by the Ts3-Query when invoking ftinitdonwload command successfully)
     * @param int $bytesToRead the length of the file to download
     */
    public function __construct(\devmx\Transmission\TransmissionInterface $transmission, $key, $bytesToRead)
    {
        $this->transmission = $transmission;
        $this->key = $key;
        $this->bytesToRead = $bytesToRead;
    }

    /**
     * Downloads the file specified by the $key
     * @return string the downloaded file 
     */
    public function download()
    {
        if (!$this->transmission->isEstablished()) $this->transmission->establish();
        $this->transmission->send($this->key);
        return $this->transmission->receiveData($this->bytesToRead);
    }

}

?>
