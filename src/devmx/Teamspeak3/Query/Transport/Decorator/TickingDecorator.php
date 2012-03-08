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
namespace devmx\Teamspeak3\Query\Transport\Decorator;
use devmx\Teamspeak3\Query\Command;

/**
 * When you decorate a query with the TickingDecorator you can define the minimum time between two commands
 * This could be useful if you get banned and there is no way to whitelist your ip
 * @author drak3
 */
class TickingDecorator extends \devmx\Teamspeak3\Query\Transport\AbstractQueryDecorator
{
    /**
     * The time to wait between two commands in seconds
     * @var int
     */
    protected $tickTime = 0;
    
    /**
     * Unix timestamp of the last command execution
     * @var int
     */
    protected $lastCommand = 0;
    
    /**
     * Sets the time to wait between two commands in seconds
     * @param int $time the time in seconds
     */
    public function setTickTime($time) {
        $this->tickTime = $time;
    }
    
    /**
     * Returns the ticktime (time to wait between two commands) in seconds
     * @return int time in seconds
     */
    public function getTickTime() {
        return $this->tickTime;
    }
    
    /**
     * Sends a command to the query and returns the results
     * @param Command $cmd
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand(Command $cmd) {
        $now = $this->getTime();
        $timeSinceLastCommand = $now - $this->lastCommand;
        if($timeSinceLastCommand < $this->tickTime) {
            $this->sleep(\ceil($this->tickTime-$timeSinceLastCommand));
        }
        $this->lastCommand = $this->getTime();
        return $this->decorated->sendCommand($cmd);
    }
    
    /**
     * Sleeps for the given amount of seconds
     * this method exists mainly to ease unittesting
     * @param int $seconds 
     */
    protected function sleep($seconds) {
        \sleep($seconds);
    }
    
    /**
     * Returns the current unix timestamp
     * this method exists mainly to ease unittesting
     * @return int
     */
    protected function getTime() {
        return \microtime(true);
    }
}

?>
