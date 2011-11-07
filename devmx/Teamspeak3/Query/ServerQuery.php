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
    protected $registerCommands = Array();
    protected $notInDefaultChannel = FALSE;
    protected $channelID;
    protected $virtualServerStatus;
    protected $virtualServerPort;
    protected $virtualServerID;
    protected $uniqueID;
    protected $nickname;
    protected $databaseID;
    protected $uniqueVirtualServerID;
    protected $clientID;
    
    public function __construct(Transport\TransportInterface $transport) {
        $this->transport = $transport;
    }
    
    public function query($name, $args, $options) {
        return $this->sendCommand(Command::simpleCommand($name, $args, $options));
    }
    
    public function refreshWhoAmI() {
        $response = $this->query("whoami");
        if(!$response->errorOccured()) {
            $this->isLoggedIn = $response['client_database_id'] === 0;
            $this->loginName = $response['login_name'];
            if(!$this->isLoggedIn)
                $this->loginPass = '';
            $this->isOnVirtualServer = $response['virtualserver_port'] === 0;
            if($this->isOnVirtualServer) {
                $this->virtualServerIdentifyer = Array('id'=>$response['virtualserver_id']);
                $this->virtualServerID = $response['virtualserver_id'];
                $this->virtualServerPort = $response['virtualserver_port'];
                $this->uniqueVirtualServerID = $response['virtualserver_unique_identifyer'];
            }
           $this->uniqueID = $response['client_unique_identifyer']; 
           $this->channelID = $response['client_channel_id'];
           $this->virtualServerStatus = $response['virtualserver_status'];
           $this->databaseID = $response['client_database_id'];
           $this->clientID = $response['client_id'];
        }
    }
    
    
    public function login($username, $pass) {
        $response = $this->query("login", Array("client_login_name"=>$username, 'client_login_password'=>$pass));
        if(!$response->errorOccured()) {
            $this->isLoggedIn = TRUE;
            $this->loginName = $username;
            $this->loginPass = $pass;
        }
        return $response;
    }
    
    public function logout() {
        $response = $this->query('logout');
        if(!$response->errorOccured()) {
            $this->isLoggedIn = FALSE;
            $this->loginName = '';
            $this->loginPass = '';
        }
        return $response;
    }
    
    public function useByPort($port) {
        $response = $this->query("use", Array('port'=>$port));
        if(!$response->errorOccured()) {
            $this->isOnVirtualServer = TRUE;
            $this->virtualServerIdentifyer = Array('port'=>$port);
        }
        return $response;
    }
    
    public function useByID($id) {
        $response = $this->query("use", Array('id'=>$id));
        if(!$response->errorOccured()) {
            $this->isOnVirtualServer = TRUE;
            $this->virtualServerIdentifyer = Array('id'=>$id);
        }
        return $response;
    }
    
    public function moveToChannel($cid) {
        if(!$this->isOnVirtualServer) {
            throw new \BadMethodCallException("cannot move to channel when not on virtual server");
        }
        $response = $this->transport->query('clientmove', Array('clid'=>$this->getClientID(), 'cid'=>$cid));
        if(!$response->errorOccured()) {
            $this->notInDefaultChannel = TRUE;
            $this->channelID = $cid;
        }
    }
    
    public function registerForEvent($name, $cid=NULL) {
        $args = Array('event'=>$name);
        if($cid !== NULL) {
            $args['cid'] = $cid;
        }
        $command = Command::simpleCommand('servernotifyregister', Array('event'=>$name));
        $response = $this->transport->sendCommand($command);
        if(!$response->errorOccured()) {
            throw new \RuntimeException("Cannot register for event $event");
        }
        else{
            $this->registerCommands[] = $command;
        }
    }
    
    public function unregisterEvents() {
        $response = $this->transport->query('servernotifyunregister');
        if(!$response->errorOccured()) {
            $this->registerCommands = Array();
        }
        else{
            throw new \RuntimeException("cannot unregister from events");
        }
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
        if($this->isLoggedIn) {
            $this->login($this->loginName, $this->loginPass);
        }
        if($this->isOnVirtualServer) {
            $this->use($this->virtualServerIdentifyer);
        }
        if($this->notInDefaultChannel) {
            $this->moveToChannel($this->channelID);
        }
        foreach($this->registerCommands as $command) {
            $this->sendCommand($command);
        }
    }
    
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }

    public function getLoginName()
    {
        return $this->loginName;

    }

    public function getLoginPass()
    {
        return $this->loginPass;

    }

    public function isOnVirtualServer()
    {
        return $this->isOnVirtualServer;
    }

    public function getVirtualServerIdentifyer()
    {
        return $this->virtualServerIdentifyer;
    }

    public function getRegisterCommands()
    {
        return $this->registerCommands;
    }

    public function notInDefaultChannel()
    {
        return $this->notInDefaultChannel;
    }

    public function getChannelID()
    {
        if($this->channelID == NULL) {
            
        }
        return $this->channelID;
    }

    public function getVirtualServerStatus()
    {
        return $this->virtualServerStatus;

    }

    public function getUniqueID()
    {
        return $this->uniqueID;

    }

    public function getNickname()
    {
        return $this->nickname;

    }

    public function getDatabaseID()
    {
        return $this->databaseID;

    }

    public function getUniqueVirtualServerID()
    {
        return $this->uniqueVirtualServerID;

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
        $args = $command->getParameters();
        if($command->getName() == 'use') {
            if(isset($args['id'])) {
                return $this->useByID($args['id']);
            }
            elseif(isset($args['port'])) {
                return $this->useByPort($args['port']);
            }
        }
        elseif($command->getName() == 'login') {
            if(isset($args['client_login_name']) && isset($args['client_login_password'])) {
                return $this->login($args['client_login_name'], $args['client_login_password']);
            }
        }
        elseif($command->getName() == 'logout') {
            $this->logout();
        }
        elseif($command->getName() == 'servernotifyregister') {
            if(isset($args['event'])) {
                if(isset($args['cid'])) {
                    return $this->registerForEvent($args['event'], $args['cid']);
                }
                else {
                    return $this->registerForEvent($args['event']);
                }
            }
        }
        elseif($command->getName() == 'servernotifyunregister') {
            
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
