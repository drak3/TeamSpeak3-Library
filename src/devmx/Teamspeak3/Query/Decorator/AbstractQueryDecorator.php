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
namespace devmx\Teamspeak3\Query\Decorator;
use \devmx\Teamspeak3\Query\Transport\Decorator\AbstractTransportDecorator;
use \devmx\Teamspeak3\Query\QueryInterface;

/**
 *
 * @author drak3
 */
abstract class AbstractQueryDecorator extends AbstractTransportDecorator implements QueryInterface
{
    
    /**
     * Constructor
     * This constructor is just in place to ensure that $query implements QueryInterface
     * @param \devmx\Teamspeak3\Query\QueryInterface $query 
     */
    public function __construct(QueryInterface $query) {
        parent::__construct($query);
    }
    
    /**
     * Refreshes the whoami information
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function refreshWhoAmI() {
        return $this->decorated->refreshWhoAmI();
    }
    
    /**
     * Logs in
     * @param string $username
     * @param string $pass
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function login($username, $pass, &$commandResponse=null) {
        return $this->decorated->login($username, $pass, $commandResponse);
    }
    
    /**
     * Logs out
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function logout(&$commandResponse=null) {
        return $this->decorated->logout($commandResponse);
    }
    
    /**
     * Selects a virtual server by port
     * @param int $port the port of the vServer to select
     * @param boolean $virtual if virtual is set, offline server will be set into a "virtual" mode. See official serverquery docs
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function useByPort($port,$virtual=TRUE, &$commandResponse=null) {
        return $this->decorated->useByPort($port, $virtual, $commandResponse);
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
        return $this->decorated->useByID($id , $virtual , $commandResponse);
    }
    
    /**
     * Deselects the currently vServer
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function deselect(&$commandResponse=null) {
        return $this->decorated->deselect($commandResponse);
    }
    
    /**
     * Moves the queryclient to a specific channel
     * @param int $cid the id of the channel
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @throws Exception\LogicException if we are'nt on a virtualserver
     */
    public function moveToChannel($cid, &$commandResponse=null) {
        return $this->decorated->moveToChannel($cid , $commandResponse);
    }
    
    /**
     * Registers for a specific event
     * @param string $name the name of the event
     * @param int|null $cid if the event is connected with a specific channel, the $cid param has to be set
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery
     */
    public function registerForEvent($name, $cid=NULL, &$commandResponse=null) {
        return $this->decorated->registerForEvent($name , $cid , $commandResponse);
    }
    
    /**
     * Unregisters from all events
     * @param mixed $commandResponse if you need the CommandResponse, you can a variable which is not null where the response will be stored in
     * @return \devmx\Teamspeak3\Query\ServerQuery 
     */
    public function unregisterEvents(&$commandResponse=null) {
        return $this->decorated->unregisterEvents($commandResponse);
    }
    
    /**
     * Changes the nickname of the queryclient
     * @param string $newNickname 
     * @param mixed $commandResponse if this var is not equal to null, the fetched commandresponse will be stored in it
     * @return \devmx\Teamspeak3\Query\ServerQuery returns self
     */
    public function changeNickname($newNickname, &$commandResponse=null) {
        return $this->decorated->changeNickname($newNickname , $commandResponse);
    }
    
    /**
     * Disconnects 
     */
    public function quit() {
        return $this->decorated->quit();
    }
    
    /**
     * If the queryclient is logged in
     * @return boolean 
     */
    public function isLoggedIn() {
        return $this->decorated->isLoggedIn();
    }
    
    /**
     * Returns the loginname used to login
     * @return string 
     */
    public function getLoginName() {
        return $this->decorated->getLoginName();
    }
    
    
    /**
     * Returns the login pass used to login
     * @return string 
     */
    public function getLoginPass() {
        return $this->decorated->getLoginPass();
    }
    
    /**
     * Wether the queryuser is on a vServer or not
     * @return boolean
     */
    public function isOnVirtualServer() {
        return $this->decorated->isOnVirtualServer();
    }
    
    /**
     * Returns the vServer port, if on a vServer
     * note that if there was no refreshWhoAmI call and the server was not selected by port the information may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getVirtualServerPort($refreshWhenInconsistent=true) {
        return $this->decorated->getVirtualServerPort($refreshWhenInconsistent);
    }
    
    /**
     * Returns the vServer id, if on a vServer
     * note that if there was no refreshWhoAmI call and the server was not selected by id the information may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getVirtualServerID($refreshWhenInconsistent=true) {
        return $this->decorated->getVirtualServerID($refreshWhenInconsistent);
    }
    
    /**
     * Returns the commands used to register for the serverevents
     * @return array
     */
    public function getRegisterCommands() {
        return $this->decorated->getRegisterCommands();
    }
    
    /**
     * Returns the current channelid
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getChannelID($refreshWhenInconsistent=true) {
        return $this->decorated->getChannelID($refreshWhenInconsistent);
    }
    
    /**
     * Returns the current vServer status
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string
     */
    public function getVirtualServerStatus($refreshWhenInconsistent=true) {
        return $this->decorated->getVirtualServerID($refreshWhenInconsistent);
    }
    
    /**
     * Returns the queryclients unique id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present) 
     * @return string 
     */
    public function getUniqueID($refreshWhenInconsistent=true) {
        return $this->decorated->getUniqueID($refreshWhenInconsistent);
    }
    
    /**
     * Returns the queryclients nickname
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated 
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string 
     */
    public function getNickname($refreshWhenInconsistent=true) {
        return $this->decorated->getNickname($refreshWhenInconsistent);
    }
    
    /**
     * Returns the queryclients database id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int
     */
    public function getDatabaseID($refreshWhenInconsistent=true) {
        return $this->decorated->getDatabaseID($refreshWhenInconsistent);
    }
    
    /**
     * Returns the unique vServer id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return string
     */
    public function getUniqueVirtualServerID($refreshWhenInconsistent=true) {
        return $this->decorated->getUniqueVirtualServerID($refreshWhenInconsistent);
    }
    
    /**
     * Returns the clients id
     * Note, that if when there was no refreshWhoAmI call, the data may be outdated
     * @param boolean $refreshWhenInconsistent if this flag is set, the method will trigger a whoami query if there is no consisten data (i.e no vServerId present)
     * @return int 
     */
    public function getClientID($refreshWhenInconsistent=true) {
        return $this->decorated->getClientID($refreshWhenInconsistent);
    }
    
    /**
     * Returns if the queryclient has registered for any command
     * @return boolean
     */
    public function hasRegisteredForEvents() {
        return $this->decorated->hasRegisteredForEvents();
    }
    
}
?>
