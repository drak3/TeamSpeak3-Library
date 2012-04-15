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
interface QueryInterface extends Transport\TransportInterface
{
    /**
     * Refreshes the whoami information
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function refreshWhoAmI();
    
    /**
     * Logs in
     * @param string $username
     * @param string $pass
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function login($username, $pass, &$commandResponse=null);
    
    /**
     * Logs out
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function logout(&$commandResponse=null);
    
    /**
     * Selects a virtual server by port
     * @param int $port the port of the vServer to select
     * @param boolean $virtual if virtual is set, offline server will be set into a "virtual" mode. See official serverquery docs
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function useByPort($port,$virtual=TRUE, &$commandResponse=null);
    
    /**
     * Selects a virtual server by id
     * @param int $id the id of the vServer to use
     * @param string $virtual if virtual is set, offline server will be set into a "virtual" mode. See official serverquery docs
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery
     * @throws Exception\InvalidArgumentException if the id is invalid
     */
    public function useByID($id,$virtual=TRUE, &$commandResponse=null);
    
    /**
     * Deselects the currently vServer
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function deselect(&$commandResponse=null);
    
    /**
     * Moves the queryclient to a specific channel
     * @param int $cid the id of the channel
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @throws Exception\LogicException if we are'nt on a virtualserver
     */
    public function moveToChannel($cid, &$commandResponse=null);
    
    /**
     * Registers for a specific event
     * @param string $name the name of the event
     * @param int|null $cid if the event is connected with a specific channel, the $cid param has to be set
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery
     */
    public function registerForEvent($name, $cid=NULL, &$commandResponse=null);
    
    /**
     * Unregisters from all events
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function unregisterEvents(&$commandResponse=null);
    
    /**
     * Changes the nickname of the queryclient
     * @param string $newNickname 
     * @param mixed $commandResponse if this var is not equal to null, the fetched commandresponse will be stored in it
     * @return \devmx\Teamspeak3\Query\ServerQuery returns self
     */
    public function changeNickname($newNickname, &$commandResponse=null);
    
    /**
     * Disconnects 
     */
    public function quit();
    
    /**
     * If the queryclient is logged in
     * @return boolean 
     */
    public function isLoggedIn();
    
    /**
     * Returns the loginname used to login
     * @return string 
     */
    public function getLoginName();
    
    
    /**
     * Returns the login pass used to login
     * @return string 
     */
    public function getLoginPass();
    
    /**
     * Wether the queryuser is on a vServer or not
     * @return boolean
     */
    public function isOnVirtualServer();
    
    /**
     * Returns the vServer port, if on a vServer
     * note that if there was no refreshWhoAmI call and the server was not selected by port the information may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getVirtualServerPort($refreshWhenInconsistent=true);
    
    /**
     * Returns the vServer id, if on a vServer
     * note that if there was no refreshWhoAmI call and the server was not selected by id the information may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getVirtualServerID($refreshWhenInconsistent=true);
    
    /**
     * Returns the commands used to register for the serverevents
     * @return array
     */
    public function getRegisterCommands();
    
    /**
     * Returns the current channelid
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getChannelID($refreshWhenInconsistent=true);
    
    /**
     * Returns the current vServer status
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string
     */
    public function getVirtualServerStatus($refreshWhenInconsistent=true);
    
    /**
     * Returns the queryclients unique id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present) 
     * @return string 
     */
    public function getUniqueID($refreshWhenInconsistent=true);
    
    /**
     * Returns the queryclients nickname
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated 
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string 
     */
    public function getNickname($refreshWhenInconsistent=true);
    
    /**
     * Returns the queryclients database id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getDatabaseID($refreshWhenInconsistent=true);
    
    /**
     * Returns the unique vServer id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string
     */
    public function getUniqueVirtualServerID($refreshWhenInconsistent=true);
    
    /**
     * Returns the clients id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int 
     */
    public function getClientID($refreshWhenInconsistent=true);
    
    /**
     * Returns if the queryclient has registered for any command
     * @return boolean
     */
    public function hasRegisteredForEvents();
        
}

?>
