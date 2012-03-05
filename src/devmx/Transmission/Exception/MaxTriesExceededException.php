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
namespace devmx\Transmission\Exception;

/**
 *
 * @author drak3
 */
class MaxTriesExceededException extends \RuntimeException implements ExceptionInterface
{
    private $tries;
    private $data;
    
    public function __construct($tries, $data) {
        parent::__construct(sprintf('Maximum of tries (%d) exceeded, this could be because of to much data to transfer or a bad connection to the host', $tries));
        $this->tries = $tries;
        $this->data = $data;
    }
    
    public function getMaximumTries() {
        return $this->tries;
    }
    
    public function getIncompleteData() {
        return $this->data;
    }
}

?>