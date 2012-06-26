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
namespace devmx\Teamspeak3\Query\Exception;

/**
 * Exception is thrown when the query sends an ban error, you have to wait the specified retryTime until the query will execute commands again
 * See the official TeamSpeak3 Query manual for information about black and whitelisting of ips
 * @author drak3
 */
class BannedException extends RuntimeException
{
    /**
     * the time to wait
     * @var int
     */
    private $retryTime;
    
    /**
     * Constructor
     * @param int $retryTime the time which should be awaited before executing commands on the query again
     */
    public function __construct($retryTime) {
        parent::__construct($this->createMessage($retryTime));
        $this->retryTime = $retryTime;
    }
    
    /**
     * Returns the time which should be awaited before executing commands on the query again
     * See the official TeamSpeak3 Query manual for information about black and whitelisting of ips
     * @return int the time after a retry makes sense
     */
    public function getRetryTime() {
        return $this->retryTime;
    }
    
    /**
     * Creates the message for this exception
     * @param int $retryTime
     * @return string 
     */
    protected function createMessage($retryTime) {
        if($retryTime > 0) {
            return sprintf('You got banned, retry in %d seconds', $retryTime);
        }
        else {
            return 'You got banned';
        }
    }
}

?>
