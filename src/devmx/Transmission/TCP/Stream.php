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
namespace devmx\Transmission\TCP;

/**
 * This class is just there to ease testing, there should be zero logic in there
 * @author drak3
 */
class Stream
{
    protected $stream;
    
    public function open($host, $port, &$errno, &$errmsg, $timeout) {
        return $this->stream = fsockopen($hostname, $port, $errno, $errmsg, $timeout);
    }
    
    public function setTimeOut($seconds, $microseconds) {
        return \stream_set_timeout($this->stream , $seconds , $microseconds);
    }
    
    public function getLine($length) {
        return \fgets($this->stream, $length);
    }
    
    public function setBlocking($mode) {
        return \stream_set_blocking($this->stream, $mode);
    }
    
    public function write($data) {
        return \frwite($this->stream, $data);
    }
    
    public function close() {
        return \fclose($this->stream);
    }
}

?>
