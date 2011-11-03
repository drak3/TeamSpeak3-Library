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
namespace devmx\Teamspeak3\Query;

/**
 *
 * @author drak3
 */
class ServerQuery implements Transport\TransportInterface
{
    
    /**
     * @varTransport\TransportInterface $transport 
     */
    protected $transport;
    
    protected $isLoggedIn = FALSE;
    protected $loginName = '';
    protected $loginPass = '';
    protected $isOnVirtualServer = FALSE;
    protected $virtualServerIdentifyer = Array();
    protected $registeredForEvents = Array();
    protected $whoAmI;
    protected $isOnChannel;
    protected $channelIdentifyer;
    
    public function __construct(Transport\TransportInterface $transport) {
        $this->transport = $transport;
    }
    
    public function __clone()
    {
        $this->transport = clone $transport;
        $this->recoverState();
    }

    public function __sleep()
    {
        $this->transport->disconnect();
    }

    public function __wakeup()
    {
        $this->transport->connect();
        $this->recoverState();
    }
    
    protected  function recoverState() {
        
    }


    public function connect()
    {
        $this->transport->connect();
    }

    public function disconnect()
    {
        $this->transport->disconnect();
    }

    public function getAllEvents()
    {
        if(!$this->hasRegisteredForEvents()) {
            throw new \LogicException("Cannot check for events when not registered for");
        }
        return $this->transport->getAllEvents();
    }

    public function isConnected()
    {
        return $this->transport->isConnected();
    }

    public function sendCommand( \devmx\Teamspeak3\Query\Command $command )
    {
        if(in_array($command,$this->stateChaningCommands)) {
            throw new \InvalidArgumentException("State changing commands can just be executed via corresponding commands");
        }
        return $this->transport->sendCommand($command);
    }

    public function waitForEvent()
    {
         if(!$this->hasRegisteredForEvents()) {
            throw new \LogicException("Cannot check for events when not registered for");
         }
         return $this->transport->waitForEvent();
    }

}

?>
