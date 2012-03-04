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
 *
 * @author drak3
 */
class BannedException extends RuntimeException
{
    private $retryTime;
    
    public function __construct($retryTime) {
        parent::__construct(sprintf('You got banned. Retry in %d seconds', $retryTime));
        $this->retryTime = $retryTime();
    }
    
    public function getRetryTime() {
        return $this->retryTime;
    }
}

?>
