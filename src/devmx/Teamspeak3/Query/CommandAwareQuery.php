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
use devmx\Teamspeak3\Query\CommandResponse;
use devmx\Teamspeak3\Query\Command;

/**
 *
 * @author drak3
 */
class CommandAwareQuery extends ServerQuery
{
    protected $exceptionOnError = false;
    
    /**
     * Sets if exceptions should be thrown when an ts3server-side error occured
     * @param type $behavior 
     */
    public function exceptionOnError($behavior) {
        $this->exceptionOnError = (boolean) $behavior;
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendCommand(Command $cmd) {
        return $this->check(parent::sendCommand($cmd));
    }
    
    /**
     * Checks if the command caused an serverside error
     * @param CommandResponse $r
     * @return \devmx\Teamspeak3\Query\CommandResponse 
     */
    protected function check(CommandResponse $r) {
        if($this->exceptionOnError) {
            $r->toException();
        }
        return $r;
    }
    
    /**
     * Fetches the servers version information including platform and build number.
     * Permissions:
     *  b_serverinstance_version_view
     * 
     * @return \devmx\Teamspeak3\Query\CommandResponse
     * Returned items contains keys version @see Response how to access them
     */
    public function version() {
        return $this->query('version');
    }
    
    /**
     * Fetches detailed connection information about the server instance including uptime, number of virtual
     * servers online, traffic information, etc.
     * For detailed information, see Server Instance Properties paragraph in the official serverquery manual.
     * Permissions:
     *  b_serverinstance_info_view
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function hostInfo() {
        return $this->query('hostinfo');
    }
    
    /**
     * Fetchs the server instance configuration including database revision number, the file transfer port, default
     * group IDs, etc.
     * For detailed information, see Server Instance Properties in the official serverquery manual.
     * Permissions:
     *  b_serverinstance_info_view
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse 
     */
    public function instanceInfo() {
        return $this->query('instanceinfo');
    }
    
    /**
     * Changes the server instance configuration using given properties.
     * For detailed information, see the Server Instance Properties in the official serverquery docs.
     * Permissions:
     *  b_serverinstance_modify_settings
     *
     * @param array $properties an array of properties that should be changed (e.g. array('serverinstance_filetransfer_port'=>40044))
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function instanceEdit(array $properties) {
        return $this->query('instanceedit', $properties);
    }
    
    /**
     * Fetches a list of IP addresses used by the server instance on multi-homed machines.
     * Permissions:
     *  b_serverinstance_binding_list
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function bindinglist() {
        return $this->query('bindinglist');
    }
    
    /**
     * Fetches a list of virtual servers including their ID, status, number of clients online, etc.
     * @param boolean $showUid if this flag is set, the servers unique identifyer is fetched along with the other data
     * @param boolean $short if this flag is set, just basic information about the servers is fetched
     * @param boolean $all  if you set this flag, the server will list all virtual servers stored in the database. This can be useful when multiple server
     *                      instances with different machine IDs are using the same database. The machine ID is used to identify the
     *                      server instance a virtual server is associated with.
     * @param boolean $onlyOffline if this flag is set, serverlist will only return offline servers
     * @return \devmx\Teamspeak3\Query\CommandResponse The items are an array containing key value arrays which are containing some information about the server
     */
    public function serverlist($showUid, $short, $all, $onlyOffline) {
        $options = array();
        
        if($showUid) {
            $options[] = 'uid';
        }
        if($short) {
            $options[] = 'short';
        }
        if($all) {
            $options[] = 'all';
        }
        if($onlyOffline) {
            $options[] = 'onlyoffline';
        }
        
        return $this->query('serverlist', array(), $options);
    }
    
    /**
     * Displays the database ID of the virtual server running on the UDP port specified by $port.
     * Permissions:
     *  b_serverinstance_virtualserver_list
     *
     * @param int $port
     * @return \devmx\Teamspeak3\Query\CommandResponse the items are in form array('server_id'=> 9987)
     */
    public function serverIdGetByPort($port) {
        return $this->query('serveridgetbyport', array('virtualserver_port' => $port));
    }
    
    /**
     * Deletes the virtual server specified with sid. Please note that only virtual servers in stopped state can be deleted.
     * Permissions:
     *  b_virtualserver_delete
     *
     * @param int $sid the id of the server to delete
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverDelete($sid) {
        return $this->query('serverdelete', array('sid'=>$sid));
    }
    
    /**
     * Creates a new virtual server using the given properties and fetches its ID, port and initial administrator
     * privilege key. If virtualserver_port is not specified, the server will test for the first unused UDP port.
     * The first virtual server will be running on UDP port 9987 by default. Subsequently started virtual servers will
     * be running on increasing UDP port numbers.
     * For detailed information, see the Virtual Server Properties paragraph in the official serverquery manual.
     * Permissions:
     *  b_virtualserver_create
     *
     * @param array $properties the initial properties for the newly created vServer (e.g. array('virtualserver_name' => 'my_name', 'virtualserver_port'=>9987)
     * @return \devmx\Teamspeak3\Query\CommandResponse on success, the items contains the new serverid ('sid'), the port ('virtualserver_port') and an admin token ('token')
     */
    public function serverCreate(array $properties=array()) {
        return $this->query('servercreate', $properties);
    }
    
    /**
     * Starts the virtual server specified with $sid. Depending on your permissions, you're able to start either your
     * own virtual server only or all virtual servers in the server instance.
     * Permissions:
     *  b_virtualserver_start_any
     *  b_virtualserver_start
     *
     * @param int $sid the id of the server to start
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverStart($sid) {
        return $this->query('serverstart', array('sid'=>$sid));
    }
    
    /**
     * Stops the virtual server specified with sid. Depending on your permissions, you're able to stop either your own
     * virtual server only or all virtual servers in the server instance.
     * Permissions:
     *   b_virtualserver_stop_any
     *   b_virtualserver_stop
     *
     * @param int $sid the id of the server to stop
     * @return \devmx\Teamspeak3\Query\CommandResponse 
     */
    public function serverStop($sid) {
        return $this->query('serverstop', array('sid'=>$sid));
    }
    
    /**
     * Stops the entire TeamSpeak 3 Server instance by shutting down the process.
     * Permissions:
     *  b_serverinstance_stop
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse 
     */
    public function serverProcessStop() {
        return $this->query('serverprocessstop');
    }
    
    /**
     * Fetches detailed configuration information about the selected virtual server including unique ID, number of
     * clients online, configuration, etc.
     * For detailed information, see the Virtual Server Properties paragraph of the official Teamspeak3 Query documentation.
     * Permissions:
     *  b_virtualserver_info_view
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse  
     */
    public function serverInfo() {
        return $this->query('serverinfo');
    }
    
    /**
     * Fetches detailed connection information about the selected virtual server including uptime, traffic
     * information, etc.
     * For detailed information, see the Virtual Server Properties paragraph of the official Teamspeak3 Query documentation (properties starting with "CONNECTION_"). 
     * Permissions:
     *  b_virtualserver_connectioninfo_view
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverRequestConnectionInfo() {
        return $this->query('serverrequestconnectioninfo');
    }
    
    /**
     * Changes the selected virtual servers configuration using given properties. Note that this command accepts
     * multiple properties which means that you're able to change all settings of the selected virtual server at once.
     * For detailed information, see the Virtual Server Properties paragraph of the official Teamspeak3 Quer documentation.
     * Permissions:
     *  b_virtualserver_modify_name
     *  b_virtualserver_modify_welcomemessage
     *  b_virtualserver_modify_maxclients
     *  b_virtualserver_modify_reserved_slots
     *  b_virtualserver_modify_password
     *  b_virtualserver_modify_default_servergroup
     *  b_virtualserver_modify_default_channelgroup
     *  b_virtualserver_modify_default_channeladmingroup
     *  b_virtualserver_modify_ft_settings
     *  b_virtualserver_modify_ft_quotas
     *  b_virtualserver_modify_channel_forced_silence
     *  b_virtualserver_modify_complain
     *  b_virtualserver_modify_antiflood
     *  b_virtualserver_modify_hostmessage
     *  b_virtualserver_modify_hostbanner
     *  b_virtualserver_modify_hostbutton
     *  b_virtualserver_modify_port
     *  b_virtualserver_modify_autostart
     *  b_virtualserver_modify_needed_identity_security_level
     *  b_virtualserver_modify_priority_speaker_dimm_modificator
     *  b_virtualserver_modify_log_settings
     *  b_virtualserver_modify_icon_id
     *  b_virtualserver_modify_weblist
     *  b_virtualserver_modify_min_client_version
     *  b_virtualserver_modify_codec_encryption_mode
     *
     * @param array $properties the properties to change
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverEdit(array $properties) {
        return $this->query('serveredit', $properties);
    }
    
    /**
     * Fetches a list of server groups available. Depending on your permissions, the output may also contain global
     * ServerQuery groups and template groups.
     * Permissions:
     *  b_serverinstance_modify_querygroup
     *  b_serverinstance_modify_templates
     *  b_virtualserver_servergroup_list
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function servergroupList() {
        return $this->query('servergrouplist');
    }
    
    /**
     * Creates a new server group using the name specified with $name and displays its ID. The optional $type
     * parameter can be used to create ServerQuery groups and template groups (0 => templategroup, 1 => regular group, 2=> Query group). For detailed information, see the 
     * Definitions section of the official Teamspeak3 Query documentation.
     * Permissions:
     *   b_virtualserver_servergroup_create
     *
     * @param string $name the name of the new group
     * @param int $type (0 => templategroup, 1 => regular group, 2=> Query group)
     * @return \devmx\Teamspeak3\Query\CommandResponse 
     */
    public function servergroupAdd($name, $type=1) {
        $args = array('name' => $name, 'type'=>$type);
        return $this->query('servergroupadd', $args);
    }
    
    /**
     * Deletes the server group specified with $sgid. If $force is set to true, the server group will be deleted even if there
     * are clients within.
     * Permissions:
     *  b_virtualserver_servergroup_delete
     *
     * @param int $sgid
     * @param boolean $force defaults to false
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function servergroupDel($sgid, $force=false) {
        $args = array('sgid' => $sgid, 'force' => $force);
        return $this->query('servergroupdel', $args);
    }
    
    /**
     * Deletes the server group specified with $sgid. If $force is set to true, the server group will be deleted even if there
     * are clients within.
     * Permissions:
     *  b_virtualserver_servergroup_delete
     * This method is an alias for servergroupDel
     * 
     * @param int $sgid
     * @param boolean $force defaults to false
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function servergroupDelete($sgid, $force=false) {
        return $this->servergroupDel($sgid , $force);
    }
    
    /**
     * Creates a copy of the server group specified with $ssgid. If $tsgid is set to 0, the server will create a new group.
     * To overwrite an existing group, simply set $tsgid to the ID of a designated target group. If a target group is set,
     * the $name parameter will be ignored.
     * The type parameter can be used to create ServerQuery groups and template groups (0 => templategroup, 1 => regular group, 2=> Query group). For detailed information,
     * see the Definitions section of the official Teamspeak3 Query documentation.
     * Permissions:
     *  b_virtualserver_servergroup_create
     *  i_group_modify_power
     *  i_group_needed_modify_power
     *
     * @param int $ssgid the id of the group to copy (source group id)
     * @param int $tsgid the id of the target group (target group id) set to 0 to create new group
     * @param string $name the name of the new group (ignored if $tsgid != 0)
     * @param int $type the type of the new group (0 => templategroup, 1 => regular group, 2=> Query group)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function servergroupCopy($ssgid, $tsgid, $name, $type=1) {
        $args = array('ssgid'=>$ssgid, 'tsgid'=>$tsgid, 'name' => $name, 'type'=>$type);
        return $this->query('servergroupcopy', $args);
    }
    
    /**
     * Changes the name of the server group specified with $sgid.
     * Permissions:
     *  i_group_modify_power
     *  i_group_needed_modify_power
     *
     * @param int $sgid the id of the group to rename
     * @param string $name the new name
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function servergroupRename($sgid, $name) {
        $args = array('sgid' => $sgid, 'name' => $name);
        return $this->query('servergrouprename', $args);
    }
    
    /**
     * Fetches a list of permissions assigned to the server group specified with sgid. If the $permsid flag is
     * set, the response will contain the permission names instead of the internal IDs.
     * Permissions:
     *  b_virtualserver_servergroup_permission_list
     *
     * @param int $sgid the servergroup id
     * @param boolean $permsid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function servergroupPermList($sgid, $permsid) {
        $args = array('sgid' => $sgid);
        $options = array();
        if($permsid) {
            $options[] = 'permsid';
        }
        return $this->query('servergrouppermlist', $args, $options);
    }
    
    /**
     * Fetches a list of permissions assigned to the server group specified with sgid. If the $permsid flag is
     * set, the response will contain the permission names instead of the internal IDs.
     * Permissions:
     *  b_virtualserver_servergroup_permission_list
     * This method is an alias for servergroupPermList
     * 
     * @param int $sgid the servergroup id
     * @param boolean $permsid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function servergroupPermissionList($sgid, $permsid) {
        return $this->servergroupPermList($sgid , $permsid);
    }
    
    public function servergroupAddPerm($sgid, array $permissions) {
        $permissions[0]['sgid'] = $sgid;
        return $this->query('servergroupaddperm', $permissions);
    }
    
}

?>
