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
use devmx\Teamspeak3\Query\Transport\TransportInterface;

/**
 * A wrapper for a TransportInterface that abstracts all commands which have directly to do with the query
 * it also adds lots of convience methods, like support for serializing with reconnect when unserialize and automaited state recovery
 * @author drak3
 */
class ServerQuery implements \devmx\Teamspeak3\Query\Transport\TransportInterface
{
    
    /**
     * The transport to wrap
     * @var Transport\TransportInterface $transport 
     */
    protected $transport;
    
    /**
     * Indicates if we are logged in
     * @var boolean
     */
    protected $isLoggedIn = false;
    
    /**
     * The name we used to login
     * @var string
     */
    protected $loginName = '';
    
    /**
     * The pass we used to login
     * @var string 
     */
    protected $loginPass = '';
    
    /**
     * Indicates if we are on a vServer (= successfully executed a use command)
     * @var boolean 
     */
    protected $isOnVirtualServer = false;
    
    /**
     * The identifyer used to select the virtualserver
     * array() if no virtualserver is selected, array('id'=><vServerID>) if used by id or array('port'=><vServerPort>) if used by port
     * @var array
     */
    protected $virtualServerIdentifyer = Array();
    
    /**
     * The commands used to register for events
     * @var array 
     */
    protected $registerCommands = Array();
    
    /**
     * The current channelid
     * @var int 
     */
    protected $channelID = 0;
    
    /**
     * The virtualServer status
     * @var string
     */
    protected $virtualServerStatus = 'unknown';
    
    /**
     * The vServer port
     * @var int 
     */
    protected $virtualServerPort = 0;
    
    /**
     * The vServer id
     * @var int 
     */
    protected $virtualServerID = 0;
    
    /**
     * The queryuser's uniqueID
     * @var string 
     */
    protected $uniqueID = '';
    
    /**
     * The queryuser's nickname
     * @var string
     */
    protected $nickname = '';
    
    /**
     * The queryuser's database id
     * @var int 
     */
    protected $databaseID = 0;
    
    /**
     * The unique vServer id if on a virtualserver
     * @var string
     */
    protected $uniqueVirtualServerID = '';
    
    /**
     * the queryuser's cid
     * @var int
     */
    protected $clientID = 0;
    
    /**
     * Constructor
     * @param TransportInterface $transport the transport to wrap
     */
    public function __construct(TransportInterface $transport) {
        $this->transport = $transport;
    }
    
    /**
     * Queries the server
     * (wrapper for sendCommand and new Command)
     * @param string $name
     * @param array $args
     * @param array $options
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function query($name, array $args=Array(), array $options=Array()) {
        return $this->sendCommand(new Command($name, $args, $options));
    }
    
    /**
     * Refreshes the whoami information
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function refreshWhoAmI() {
        $response = $this->transport->query('whoami');
        if(!$response->errorOccured()) {
            $this->loginName = $response['client_login_name'];
            $this->isLoggedIn = $this->loginName != '';
            if(!$this->isLoggedIn)
                $this->loginPass = '';
            $this->isOnVirtualServer =($response['virtualserver_port'] !== 0);
            if($this->isOnVirtualServer) {
                $this->virtualServerIdentifyer = Array('id'=>$response['virtualserver_id']);
            }
            $this->virtualServerID = $response['virtualserver_id'];
            $this->virtualServerPort = $response['virtualserver_port'];
            $this->uniqueVirtualServerID = $response['virtualserver_unique_identifyer'];
            $this->uniqueID = $response['client_unique_identifyer']; 
            $this->channelID = $response['client_channel_id'];
            $this->virtualServerStatus = $response['virtualserver_status'];
            $this->databaseID = $response['client_database_id'];
            $this->clientID = $response['client_id'];
            $this->nickname = $response['client_nickname'];
        }
        else {
            $response->toException();
        }
        return $response;
    }
    
    /**
     * Logs in
     * @param string $username
     * @param string $pass
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function login($username, $pass, &$commandResponse=null) {
        $response = $this->transport->query("login", Array("client_login_name"=>$username, 'client_login_password'=>$pass));
        $response->toException();
        $this->isLoggedIn = TRUE;
        $this->loginName = $username;
        $this->loginPass = $pass;
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Logs out
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function logout(&$commandResponse=null) {
        $response = $this->transport->query('logout');
        $response->toException();
        $this->isLoggedIn = FALSE;
        $this->loginName = '';
        $this->loginPass = '';
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Selects a virtual server by port
     * @param int $port the port of the vServer to select
     * @param boolean $virtual if virtual is set, offline server will be set into a "virtual" mode. See official serverquery docs
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function useByPort($port,$virtual=TRUE, &$commandResponse=null) {
        $options = $virtual ? Array('virtual') : Array();
        $response = $this->transport->query("use", Array('port'=>$port), $options);
        $response->toException();
        $this->isOnVirtualServer = TRUE;
        $this->virtualServerIdentifyer = Array('port'=>$port);
        $this->virtualServerPort = $port;
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Selects a virtual server by id
     * @param int $id the id of the vServer to use
     * @param string $virtual if virtual is set, offline server will be set into a "virtual" mode. See official serverquery docs
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery
     * @throws Exception\InvalidArgumentException if the id is invalid
     */
    public function useByID($id,$virtual=TRUE, &$commandResponse=null) {
        if($id < 1) {
            throw new Exception\InvalidArgumentException("Invalid server ID, if you want to deselect the current server, please use deselect() instead");
        }
        $options = $virtual ? Array('virtual') : Array();
        $response = $this->transport->query("use", Array('sid'=>$id), $options);
        $response->toException();
        $this->isOnVirtualServer = TRUE;
        $this->virtualServerIdentifyer = Array('id'=>$id);
        $this->virtualServerID = $id;
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Decides how to select the virtual server by the $args param
     * @param array $args
     * @param boolean $virtual
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    protected function useVirtualServer($args, $virtual, &$commandResponse=null) {
       if(isset($args['sid'])) {
            return $this->useByID($args['sid'], $virtual, $commandResponse); 
       }
       elseif(isset($args['port'])) {
            return $this->useByPort($args['port'], $virtual, $commandResponse);
       }
    }
    
    /**
     * Deselects the currently vServer
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function deselect(&$commandResponse=null) {
        $response = $this->transport->query('use');
        $response->toException();
        $this->isOnVirtualServer = FALSE;
        $this->virtualServerIdentifyer = Array();
        $this->virtualServerID = 0;
        $this->virtualServerPort = 0;
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Moves the queryclient to a specific channel
     * @param int $cid the id of the channel
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @throws Exception\LogicException if we are'nt on a virtualserver
     */
    public function moveToChannel($cid, &$commandResponse=null) {
        if(!$this->isOnVirtualServer) {
            throw new Exception\LogicException("Cannot move to channel when not on virtual server.");
        }
        $response = $this->transport->query('clientmove', Array('clid'=>$this->getClientID(), 'cid'=>$cid));
        $response->toException();
        $this->channelID = $cid;
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Registers for a specific event
     * @param string $name the name of the event
     * @param int|null $cid if the event is connected with a specific channel, the $cid param has to be set
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery
     */
    public function registerForEvent($name, $cid=NULL, &$commandResponse=null) {
        $args = Array('event'=>$name);
        if($cid !== NULL) {
            $args['id'] = $cid;
        }
        $command = new Command('servernotifyregister', $args);
        $response = $this->transport->sendCommand($command);
        $response->toException();
        $this->registerCommands[] = $command;
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Unregisters from all events
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function unregisterEvents(&$commandResponse=null) {
        $response = $this->transport->query('servernotifyunregister');
        $response->toException();
        $this->registerCommands = Array();
        if($commandResponse !== null) {
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Changes the nickname of the queryclient
     * @param string $newNickname 
     * @param mixed $commandResponse if this var is not equal to null, the fetched commandresponse will be stored in it
     * @return \devmx\Teamspeak3\Query\ServerQuery returns self
     */
    public function changeNickname($newNickname, &$commandResponse=null) {
        if(!$this->isOnVirtualServer()) {
            throw new Exception\LogicException("Cannot change nickname when not on virtual server");
        }
        $response = $this->transport->query('clientedit', array('clid'=>$this->getClientID(), 'client_nickname'=>$newNickname));
        $response->toException();
        $this->nickname = $newNickname;
        if($commandResponse !== null ){
            $commandResponse = $response;
        }
        return $this;
    }
    
    /**
     * Disconnects 
     */
    public function quit() {
        $this->transport->disconnect();
    }
    
    /**
     * Clones the query, the whole state is recovered 
     */
    public function __clone()
    {
        $this->transport = clone $transport;
        $this->recoverState();
    }
    
    
    /**
     * Magic sleep method
     */
    public function __sleep()
    {
        $this->transport->disconnect();
        return array(
            'transport',
            'isLoggedIn', 
            'loginName', 
            'loginPass', 
            'isOnVirtualServer', 
            'virtualServerIdentifyer', 
            'registerCommands', 
            'channelID', 
            'virtualServerStatus',
            'virtualServerID',
            'uniqueID',
            'nickname',
            'databaseID',
            'uniqueVirtualServerID',
            'clientID',
        );
    }
    
    /**
     * Wakes up the query, recovers the whole state 
     */
    public function __wakeup()
    {
            $this->recoverState();
    }
    
    /**
     * Recovers the state of the query 
     */
    protected  function recoverState() {
        if($this->isConnected()) {
            $this->transport->connect();
            if($this->isLoggedIn) {
                $this->login($this->loginName, $this->loginPass);
            }
            if($this->isOnVirtualServer) {
                $this->useVirtualServer($this->virtualServerIdentifyer, true);
            }
            if($this->channelID !== 0) {
                $this->moveToChannel($this->channelID);
            }
            foreach($this->registerCommands as $command) {
                $this->transport->sendCommand($command);
            }
        }        
    }
    
    /**
     * If the queryclient is logged in
     * @return boolean 
     */
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }
    
    /**
     * Returns the loginname used to login
     * @return string 
     */
    public function getLoginName()
    {
        return $this->loginName;
    }
    
    /**
     * Returns the login pass used to login
     * @return string 
     */
    public function getLoginPass()
    {
        return $this->loginPass;
    }
    
    /**
     * Wether the queryuser is on a vServer or not
     * @return boolean
     */
    public function isOnVirtualServer()
    {
        return $this->isOnVirtualServer;
    }
    
    /**
     * Returns the vServer port, if on a vServer
     * note that if there was no refreshWhoAmI call and the server was not selected by port the information may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getVirtualServerPort($refreshWhenInconsistent=true) {
        if($this->virtualServerID === 0 && $this->isOnVirtualServer() && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->virtualServerPort;
    }
    
    /**
     * Returns the vServer id, if on a vServer
     * note that if there was no refreshWhoAmI call and the server was not selected by id the information may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getVirtualServerID($refreshWhenInconsistent=true) {
        if($this->virtualServerID === 0 && $this->isOnVirtualServer() && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->virtualServerID;
    }
    
    /**
     * Returns the commands used to register for the serverevents
     * @return array
     */
    public function getRegisterCommands()
    {
        return $this->registerCommands;
    }
    
    /**
     * Returns the current channelid
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getChannelID($refreshWhenInconsistent=true)
    {
        if($this->channelID === 0 && $this->isOnVirtualServer() && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->channelID;
    }
    
    /**
     * Returns the current vServer status
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string
     */
    public function getVirtualServerStatus($refreshWhenInconsistent=true)
    {
        if($this->virtualServerStatus === 'unknown' && $this->isOnVirtualServer() && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->virtualServerStatus;
    }
    
    /**
     * Returns the queryclients unique id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present) 
     * @return string 
     */
    public function getUniqueID($refreshWhenInconsistent=true)
    {
        if($this->uniqueID === '' && $this->isOnVirtualServer && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->uniqueID;
    }
    
    /**
     * Returns the queryclients nickname
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated 
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string 
     */
    public function getNickname($refreshWhenInconsistent=true)
    {
        if($this->nickname === '' && $this->isOnVirtualServer && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->nickname;
    }
    
    /**
     * Returns the queryclients database id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getDatabaseID($refreshWhenInconsistent=true)
    {
        if($this->databaseID === 0 && $this->isOnVirtualServer() && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->databaseID;
    }
    
    /**
     * Returns the unique vServer id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string
     */
    public function getUniqueVirtualServerID($refreshWhenInconsistent=true)
    {
        if($this->uniqueVirtualServerID === '' && $this->isOnVirtualServer() && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->uniqueVirtualServerID;
    }
    
    /**
     * Returns the clients id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int 
     */
    public function getClientID($refreshWhenInconsistent=true) {
        if($this->clientID === 0 && $this->isOnVirtualServer() && $refreshWhenInconsistent) {
            $this->refreshWhoAmI();
        }
        return $this->clientID;
    }
    
    /**
     * Returns if the queryclient has registered for any command
     * @return boolean
     */
    public function hasRegisteredForEvents() {
        return $this->registerCommands != array();
    }

    /**
     * Connects the query 
     * @return devmx\Teamspeak3\Query\ServerQuery returns itself
     */
    public function connect()
    {
        $this->transport->connect();
        return $this;
    }
    
    /**
     * Disconnects the query 
     * @return devmx\Teamspeak3\Query\ServerQuery returns itself
     */
    public function disconnect()
    {
        $this->transport->disconnect();
        return $this;
    }
    
    /**
     * Returns all events occured since last time checking the query
     * This method is non-blocking, so it returns even if no event is on the query
     * @return array Array of all events lying on the query  
     * @throws Exception\LogicException if the query is not registered for any events
     */
    public function getAllEvents()
    {
        if(!$this->hasRegisteredForEvents()) {
            throw new Exception\LogicException("Cannot check for events when not registered for");
        }
        return $this->transport->getAllEvents();
    }
    
    /**
     * Returns if the query is connected
     * @return boolean
     */
    public function isConnected()
    {
        return $this->transport->isConnected();
    }
    
    /**
     * Sends a command to the query and returns the result plus all occured events
     * This method is aware of all querystate changing commands like use
     * @param Command $command
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendCommand( \devmx\Teamspeak3\Query\Command $command )
    {
        $args = $command->getParameters();
        $response = '';
        if($command->getName() == 'use') {
            $this->useVirtualServer($args, in_array('virtual', $command->getOptions()), $response);
        }
        if($command->getName() == 'login') {
            if(isset($args['client_login_name']) && isset($args['client_login_password'])) {
                $this->login($args['client_login_name'], $args['client_login_password'], $response);
            }
        }
        if($command->getName() == 'logout') {
            $this->logout($response);
        }
        if($command->getName() == 'servernotifyregister') {
            if(isset($args['event'])) {
                if(isset($args['id'])) {
                    $this->registerForEvent($args['event'], $args['id'], $response);
                }
                else {
                    $this->registerForEvent($args['event'], null, $response);
                }
            }
        }
        if($command->getName() == 'servernotifyunregister') {
            $this->unregisterEvents($response);
        }
        if($command->getName() === 'whoami') {
            return $this->refreshWhoAmI();
        }
        if($response !== '') {
            return $response;
        }
        return $this->transport->sendCommand($command);
    }

    /**
     * Waits until an event occurs
     * This method is blocking, it returns only if a event occurs, so avoid calling this method if you aren't registered to any events
     * @param float the timeout in second how long to wait for an event. If there is no event after the given timeout, an empty array is returned
     *   -1 means that the method may wait forever
     * @return array array of all occured events (e.g if two events occur together it is possible to get 2 events) 
     * @throws Exception\LogicException if the query is not registered for any events
     */
    public function waitForEvent($timeout=-1)
    {
         if(!$this->hasRegisteredForEvents()) {
            throw new Exception\LogicException("Cannot check for events when not registered for");
         }
         return $this->transport->waitForEvent($timeout);
    }
    
    /**
     * Returns the underlying transport
     * @return \devmx\Teamspeak3\Query\Transport\TransportInterface
     */
    public function getTransport() {
        return $this->transport;
    }

}

?>
