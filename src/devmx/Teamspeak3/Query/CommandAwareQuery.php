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
use devmx\Teamspeak3\Query\Response;

/**
 * This class wraps all queries from the offical server query documentation into methods with dedicated parameters.
 * The offical server query documentation can be found here {@link http://media.teamspeak.com/ts3_literature/TeamSpeak%203%20Server%20Query%20Manual.pdf}
 *
 * @author Martin Parsiegla <martin.parsiegla@speanet.info>
 */
class CommandAwareQuery extends ServerQuery
{
    protected $exceptionOnError = false;

    /**
     * Sets if exceptions should be thrown when an ts3server-side error occured
     * @param type $behavior
     */
    public function exceptionOnError($behavior)
    {
        $this->exceptionOnError = (boolean)$behavior;
    }

    /**
     * {@inheritdoc}
     */
    public function sendCommand(Command $cmd)
    {
        return $this->check(parent::sendCommand($cmd));
    }

    /**
     * Checks if the command caused an serverside error.
     *
     * @param CommandResponse $r
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    protected function check(CommandResponse $r)
    {
        if ($this->exceptionOnError) {
            $r->toException();
        }
        return $r;
    }

    /**
     * Fetches the servers version information including platform and build number.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     * Returned items contains keys version @see Response how to access them
     */
    public function version()
    {
        return $this->query('version');
    }

    /**
     * Fetches detailed connection information about the server instance including uptime, number of virtual
     * servers online, traffic information, etc.
     * For detailed information, see Server Instance Properties paragraph in the official serverquery manual.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function hostInfo()
    {
        return $this->query('hostinfo');
    }

    /**
     * Fetchs the server instance configuration including database revision number, the file transfer port, default
     * group IDs, etc.
     * For detailed information, see Server Instance Properties in the official serverquery manual.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function instanceInfo()
    {
        return $this->query('instanceinfo');
    }

    /**
     * Changes the server instance configuration using given properties.
     * For detailed information, see the Server Instance Properties in the official serverquery docs.
     *
     * @param array $properties an array of properties that should be changed (e.g. array('serverinstance_filetransfer_port'=>40044))
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function instanceEdit(array $properties)
    {
        return $this->query('instanceedit', $properties);
    }

    /**
     * Fetches a list of IP addresses used by the server instance on multi-homed machines.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function bindingList()
    {
        return $this->query('bindinglist');
    }

    /**
     * Fetches a list of virtual servers including their ID, status, number of clients online, etc.
     * @param boolean $showUid if this flag is set, the servers unique identifyer is fetched along with the other data
     * @param boolean $short if this flag is set, just basic information about the servers is fetched
     * @param boolean $showAll  if you set this flag, the server will list all virtual servers stored in the database. This can be useful when multiple server
     *                      instances with different machine IDs are using the same database. The machine ID is used to identify the
     *                      server instance a virtual server is associated with.
     * @param boolean $showOnlyOffline if this flag is set, serverlist will only return offline servers
     * @return \devmx\Teamspeak3\Query\CommandResponse The items are an array containing key value arrays which are containing some information about the server
     */
    public function serverList($showUid = false, $short = false, $showAll = false, $showOnlyOffline = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query('serverlist', array(), $options);
    }

    /**
     * Displays the database ID of the virtual server running on the UDP port specified by $port.
     *
     * @param int $port
     * @return \devmx\Teamspeak3\Query\CommandResponse the items are in form array('server_id'=> 9987)
     */
    public function serverIdGetByPort($port)
    {
        return $this->query('serveridgetbyport', array('virtualserver_port' => $port));
    }

    /**
     * Deletes the virtual server specified with sid. Please note that only virtual servers in stopped state can be deleted.
     *
     * @param int $sid the id of the server to delete
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverDelete($sid)
    {
        return $this->query('serverdelete', array('sid' => $sid));
    }

    /**
     * Creates a new virtual server using the given properties and fetches its ID, port and initial administrator
     * privilege key. If virtualserver_port is not specified, the server will test for the first unused UDP port.
     * The first virtual server will be running on UDP port 9987 by default. Subsequently started virtual servers will
     * be running on increasing UDP port numbers.
     * For detailed information, see the Virtual Server Properties paragraph in the official serverquery manual.
     *
     * @param string $name The name of the virtual server
     * @param array $properties the initial properties for the newly created vServer (e.g. array('virtualserver_name' => 'my_name', 'virtualserver_port'=>9987)
     * @return \devmx\Teamspeak3\Query\CommandResponse on success, the items contains the new serverid ('sid'), the port ('virtualserver_port') and an admin token ('token')
     */
    public function serverCreate($name, array $properties = array())
    {
        $args = array("virtualserver_name" => $name);
        $args = array_merge($args, $properties);

        return $this->query('servercreate', $args);
    }

    /**
     * Starts the virtual server specified with $sid. Depending on your permissions, you're able to start either your
     * own virtual server only or all virtual servers in the server instance.
     *
     * @param int $serverId the id of the server to start
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverStart($serverId)
    {
        return $this->query('serverstart', array('sid' => $serverId));
    }

    /**
     * Stops the virtual server specified with sid. Depending on your permissions, you're able to stop either your own
     * virtual server only or all virtual servers in the server instance.
     *
     * @param int $serverId the id of the server to stop
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverStop($serverId)
    {
        return $this->query('serverstop', array('sid' => $serverId));
    }

    /**
     * Stops the entire TeamSpeak 3 Server instance by shutting down the process.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverProcessStop()
    {
        return $this->query('serverprocessstop');
    }

    /**
     * Fetches detailed configuration information about the selected virtual server including unique ID, number of
     * clients online, configuration, etc.
     * For detailed information, see the Virtual Server Properties paragraph of the official Teamspeak3 Query documentation.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverInfo()
    {
        return $this->query('serverinfo');
    }

    /**
     * Fetches detailed connection information about the selected virtual server including uptime, traffic
     * information, etc.
     * For detailed information, see the Virtual Server Properties paragraph of the official Teamspeak3 Query documentation (properties starting with "CONNECTION_").
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverRequestConnectionInfo()
    {
        return $this->query('serverrequestconnectioninfo');
    }

    /**
     * Sets a new temporary server password specified with pw. The temporary password will be valid for the
     * number of seconds specified with duration. The client connecting with this password will automatically join
     * the channel specified with tcid. If tcid is set to 0, the client will join the default channel.
     *
     * @param string $password
     * @param string $description
     * @param int $duration
     * @param int $targetChannelId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverTempPasswordAdd($password, $description, $duration, $targetChannelId = 0)
    {
        $args = array(
            "pw" => $password,
            "desc" => $description,
            "duration" => (int)$duration,
            "tcid" => $targetChannelId,
        );

        return $this->query("servertemppasswordadd", $args);
    }

    /**
     * Deletes the temporary server password specified with pw.
     *
     * @param $password
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverTempPasswordDel($password)
    {
        $args = array("pw" => $password);

        return $this->query("servertemppassworddel", $args);
    }

    /**
     * Returns a list of active temporary server passwords. The output contains the clear-text password, the
     * nickname and unique identifier of the creating client.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverTempPasswordList()
    {
        return $this->query("servertemppasswordlist");
    }

    /**
     * Changes the selected virtual servers configuration using given properties. Note that this command accepts
     * multiple properties which means that you're able to change all settings of the selected virtual server at once.
     * For detailed information, see the Virtual Server Properties paragraph of the official Teamspeak3 Quer documentation.
     *
     * @param array $properties the properties to change
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverEdit(array $properties)
    {
        return $this->query('serveredit', $properties);
    }

    /**
     * Fetches a list of server groups available. Depending on your permissions, the output may also contain global
     * ServerQuery groups and template groups.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupList()
    {
        return $this->query('servergrouplist');
    }

    /**
     * Creates a new server group using the name specified with $name and displays its ID. The optional $type
     * parameter can be used to create ServerQuery groups and template groups (0 => templategroup, 1 => regular group, 2=> Query group). For detailed information, see the
     * Definitions section of the official Teamspeak3 Query documentation.
     *
     * @param string $name the name of the new group
     * @param int|null $type (0 => templategroup, 1 => regular group, 2=> Query group)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupAdd($name, $type = 1)
    {
        $args = array(
            'name' => $name,
            'type' => $type
        );

        return $this->query('servergroupadd', $args);
    }

    /**
     * Deletes the server group specified with $sgid. If $force is set to true, the server group will be deleted even if there
     * are clients within.
     *
     * @param int $sgid
     * @param boolean $force defaults to false
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupDel($sgid, $force = false)
    {
        $args = array('sgid' => $sgid, 'force' => $force);

        return $this->query('servergroupdel', $args);
    }

    /**
     * Creates a copy of the server group specified with $ssgid. If $tsgid is set to 0, the server will create a new group.
     * To overwrite an existing group, simply set $tsgid to the ID of a designated target group. If a target group is set,
     * the $name parameter will be ignored.
     * The type parameter can be used to create ServerQuery groups and template groups (0 => templategroup, 1 => regular group, 2=> Query group). For detailed information,
     * see the Definitions section of the official Teamspeak3 Query documentation.
     *
     * @param int $ssgid the id of the group to copy (source group id)
     * @param int $tsgid the id of the target group (target group id) set to 0 to create new group
     * @param string $name the name of the new group (ignored if $tsgid != 0)
     * @param int $type the type of the new group (0 => templategroup, 1 => regular group, 2=> Query group)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupCopy($ssgid, $tsgid, $name, $type = 1)
    {
        $args = array(
            'ssgid' => $ssgid,
            'tsgid' => $tsgid,
            'name' => $name,
            'type' => $type
        );

        return $this->query('servergroupcopy', $args);
    }

    /**
     * Changes the name of the server group specified with $sgid.
     *
     * @param int $sgid the id of the group to rename
     * @param string $name the new name
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupRename($sgid, $name)
    {
        $args = array(
            'sgid' => $sgid,
            'name' => $name
        );

        return $this->query('servergrouprename', $args);
    }

    /**
     * Fetches a list of permissions assigned to the server group specified with sgid. If the $permsid flag is
     * set, the response will contain the permission names instead of the internal IDs.
     *
     * @param int $sgid the servergroup id
     * @param boolean $permsid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupPermList($sgid, $permsid = false)
    {
        $args = array('sgid' => $sgid);
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query('servergrouppermlist', $args, $options);
    }

    /**
     * Adds a set of specified permissions to the server group specified with sgid. Multiple permissions can be added
     * by providing the four parameters of each permission. A permission can be specified by permid or permsid.
     *
     * @param integer $sgid
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupAddPerm($sgid, array $permissions)
    {
        $args = $permissions;
        $args["sgid"] = $sgid;

        return $this->query("servergroupaddperm", $args);
    }

    /**
     * Removes a specified permissions from the server group specified with sgid. A permission can be specified by permid or permsid.
     *
     * @param integer $sgid
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupDelPerm($sgid, array $permissions)
    {
        $args = $permissions;
        $args["sgid"] = $sgid;

        return $this->query("servergroupdelperm", $args);
    }

    /**
     * Adds a client to the server group specified with sgid. Please note that a client cannot be added to default
     * groups or template groups.
     *
     * @param integer $sgid
     * @param integer $clientId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupAddClient($sgid, $clientDbId)
    {
        $args = array(
            "sgid" => $sgid,
            "cldbid" => $clientDbId
        );

        return $this->query("servergroupaddclient", $args);
    }

    /**
     * Removes a client specified with $clientDbId from the server group specified with $sgid.
     *
     * @param integer $sgid
     * @param integer $clientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupDelClient($sgid, $clientDbId)
    {
        $args = array(
            "sgid" => $sgid,
            "cldbid" => $clientDbId
        );

        return $this->query("servergroupdelclient", $args);
    }

    /**
     * Displays the IDs of all clients currently residing in the server group specified with $sgid. If you're using the
     * optional $showNames option, the output will also contain the last known nickname and the unique identifier of the clients.
     *
     * @param integer $sgid
     * @param bool $showNames
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupClientList($sgid, $showNames = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query("servergroupclientlist", array("sgid" => $sgid), $options);
    }

    /**
     * Displays all server groups the client specified with $clientDbId is currently residing in.
     *
     * @param integer $clientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupsByClientId($clientDbId)
    {
        return $this->query("servergroupsbyclientid", array("cldbid" => $clientDbId));
    }

    /**
     * Adds a specified permissions to *ALL* regular server groups on all virtual servers. The target groups will
     * be identified by the value of their i_group_auto_update_type permission specified with $serverGroupType.
     * Multiple permissions can be added at once. A permission can be specified by permid or permsid.
     *
     * The known values for $serverGroupType are:
     *  10: Channel Guest
     *  15: Server Guest
     *  20: Query Guest
     *  25: Channel Voice
     *  30: Server Normal
     *  35: Channel Operator
     *  40: Channel Admin
     *  45: Server Admin
     *  50: Query Admin
     *
     * @param integer $serverGroupType
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupAutoAddPerm($serverGroupType, array $permissions)
    {
        $args = $permissions;
        $args["sgtype"] = $serverGroupType;

        return $this->query("servergroupautoaddperm", $args);
    }

    /**
     * Removes a specified permissions from *ALL* regular server groups on all virtual servers. The target
     * groups will be identified by the value of their i_group_auto_update_type permission specified with $serverGroupType.
     * Multiple permissions can be removed at once. A permission can be specified by permid or permsid.
     *
     * The known values for $serverGroupType are:
     *  10: Channel Guest
     *  15: Server Guest
     *  20: Query Guest
     *  25: Channel Voice
     *  30: Server Normal
     *  35: Channel Operator
     *  40: Channel Admin
     *  45: Server Admin
     *  50: Query Admin
     *
     * @param integer $serverGroupType
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverGroupAutoDelPerm($serverGroupType, array $permissions)
    {
        $args = $permissions;
        $args["sgtype"] = $serverGroupType;

        return $this->query("servergroupautodelperm", $args);
    }

    /**
     * Displays a snapshot of the selected virtual server containing all settings, groups and known client identities.
     * The data from a server snapshot can be used to restore a virtual servers configuration, channels and
     * permissions using the serversnapshotdeploy command.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverSnapshotCreate()
    {
        return $this->query("serversnapshotcreate");
    }

    /**
     * Restores the selected virtual servers configuration using the data from a previously created server snapshot.
     * Please note that the TeamSpeak 3 Server does NOT check for necessary permissions while deploying a
     * snapshot so the command could be abused to gain additional privileges.
     *
     * @param array|\devmx\Teamspeak3\Query\Response $value
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverSnapshotDeploy($value)
    {
        if(is_array($value)) {
            $items = $value;
        } elseif($value instanceof Response) {
            $items = $value->getItems();
        } else {
            throw new \InvalidArgumentException('$value must be an array or an instance of devmx\Teamspeak3\Query\Response');
        }

        return $this->query("serversnapshotdeploy", $items);
    }

    /**
     * Registers for a specified category of events on a virtual server to receive notification messages. Depending on
     * the notifications you've registered for, the server will send you a message on every event in the view of your
     * ServerQuery client (e.g. clients joining your channel, incoming text messages, server configuration changes, etc).
     * The event source is declared by the event parameter while id can be used to limit the notifications to a specific channel.
     *
     * @param string $event (server, channel, textserver, textchannel, textprivate)
     * @param null|integer $channelId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverNotifyRegister($event, $channelId = null)
    {
        $args = array("event" => $event);
        if (null !== $args) {
            $args["id"] = $channelId;
        }

        return $this->query("servernotifyregister", $args);
    }

    /**
     * Unregisters all events previously registered with servernotifyregister so you will no longer receive
     * notification messages.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function serverNotifyUnregister()
    {
        return $this->query("servernotifyunregister");
    }

    /**
     * Sends a text message a specified target. The type of the target is determined by targetmode while target
     * specifies the ID of the recipient, whether it be a virtual server, a channel or a client.
     *
     * @param integer $targetmode (1 => client, 2 => channel, 3 => virtual server)
     * @param integer $targetId The ID of the server|client|channel, depending on the targetmode.
     * @param $message
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function sendTextMessage($targetmode, $targetId, $message)
    {
        $args = array(
            "targetmode" => $targetmode,
            "target" => $targetId,
            "msg" => $message
        );

        return $this->query("sendtextmessage", $args);
    }

    /**
     * Displays a specified number of entries from the servers log. If instance is set to true, the server will
     * return lines from the master logfile (ts3server_0.log) instead of the selected virtual server logfile.
     *
     * @param null|int $lines
     * @param null|int $beginPos
     * @param null|int $reverse
     * @param null|int $instance
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function logView($lines = null, $beginPos = null, $reverse = null, $instance = null)
    {
        $args = array();
        if (null !== $lines) {
            $args["lines"] = (int)$lines;
        }

        if (null !== $beginPos) {
            $args["begin_pos"] = (int)$beginPos;
        }

        if (null !== $reverse) {
            $args["reverse"] = (int)$reverse;
        }

        if (null !== $instance) {
            $args["instance"] = (int)$instance;
        }

        return $this->query("logview", $args);
    }

    /**
     * Writes a custom entry into the servers log. Depending on your permissions, you'll be able to add entries into
     * the server instance log and/or your virtual servers log. The loglevel parameter specifies the type of the entry.
     *
     * @param string $message
     * @param int $logLevel (1 => error, 2 => warning, 3 => debug, 4 => info)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function logAdd($message, $logLevel = 4)
    {
        $args = array(
            "loglevel" => $logLevel,
            "logmsg" => $message
        );

        return $this->query("logadd", $args);
    }

    /**
     * Sends a text message to all clients on all virtual servers in the TeamSpeak 3 Server instance.
     *
     * @param string $message
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function gm($message)
    {
        return $this->query("gm", array("msg" => $message));
    }

    /**
     * Displays a list of channels created on a virtual server including their ID, order, name, etc.
     *
     * @param bool $showTopic
     * @param bool $showFlags
     * @param bool $showVoice
     * @param bool $showLimits
     * @param bool $showIcon
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelList($showTopic = false, $showFlags = false, $showVoice = false, $showLimits = false, $showIcon = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query("channellist", array(), $options);
    }

    /**
     * Displays detailed configuration information about a channel including ID, topic, description, etc.
     * For detailed information, see the Channel Properties paragraph of the official Teamspeak3 Query documentation.
     *
     * @param integer $channelId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelInfo($channelId)
    {
        return $this->query("channelinfo", array("cid" => $channelId));
    }

    /**
     * Displays a list of channels matching a given name pattern.
     *
     * @param string $pattern
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelFind($pattern)
    {
        return $this->query("channelfind", array("pattern" => $pattern));
    }

    /**
     * Moves a channel to a new parent channel with the ID $parentId. If order is specified, the channel will be sorted right
     * under the channel with the specified ID. If order is set to 0, the channel will be sorted right below the new parent.
     *
     * @param integer $channelId
     * @param integer $parentId
     * @param null|integer $order
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelMove($channelId, $parentId, $order = null)
    {
        $args = array(
            "cid" => (int)$channelId,
            "cpid" => (int)$parentId,
        );

        if (null !== $order) {
            $args["order"] = (int)$order;
        }

        return $this->query("channelmove", $args);
    }

    /**
     * Creates a new channel using the given properties and displays its ID. Note that this command
     * accepts multiple properties which means that you're able to specifiy all settings of the new channel at once.
     * For detailed information, see the Channel Properties paragraph of the official Teamspeak3 Query documentation.
     *
     * @param string $name
     * @param array $properties
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelCreate($name, array $properties = array())
    {
        $args = array("channel_name" => $name);
        $args = array_merge($args, $properties);

        return $this->query("channelcreate", $args);
    }

    /**
     * Deletes an existing channel by $channelId. If force is set to true, the channel will be deleted even if there
     * are clients within. The clients will be kicked to the default channel with an appropriate reason message.
     *
     * @param integer $channelId
     * @param bool $force
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelDelete($channelId, $force = false)
    {
        $args = array(
            "cid" => $channelId,
            "force" => $force
        );

        return $this->query("channeldelete", $args);
    }

    /**
     * Changes a channels configuration using given properties. Note that this command accepts multiple properties
     * which means that you're able to change all settings of the channel specified with cid at once.
     * For detailed information, see the Channel Properties paragraph of the official Teamspeak3 Query documentation.
     *
     * @param integer $channelId
     * @param array $properties
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelEdit($channelId, array $properties)
    {
        $args = array("cid" => $channelId);
        $args = array_merge($args, $properties);


        return $this->query("channeledit", $args);
    }

    /**
     * Displays a list of channel groups available on the selected virtual server.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupList()
    {
        return $this->query("channelgrouplist");
    }

    /**
     * Creates a new channel group using a given name and displays its ID. The optional type parameter can be
     * used to create ServerQuery groups and template groups.
     *
     * @param string $name
     * @param integer (0 => templategroup, 1 => regular group, 2 => Query group)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupAdd($name, $type = 1)
    {
        $args = array(
            "name" => $name,
            "type" => $type
        );

        return $this->query("channelgroupadd", $args);
    }

    /**
     * Deletes a channel group by ID. If force is set to true, the channel group will be deleted even if
     * there are clients within.
     *
     * @param integer $cgid
     * @param bool $force
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupDel($cgid, $force = false)
    {
        $args = array(
            "cgid" => $cgid,
            "force" => $force
        );

        return $this->query("channelgroupdel", $args);
    }

    /**
     * Creates a copy of the channel group specified with scgid. If tcgid is set to 0, the server will create a new group.
     * To overwrite an existing group, simply set tcgid to the ID of a designated target group. If a target group is set,
     * the name parameter will be ignored. The type parameter can be used to create ServerQuery groups and template groups.
     *
     * @param integer $scgid The id of the channel group source.
     * @param integer $tsgid If not 0 the source channel group will be copied to the target channel group.
     * @param string $name The name of the new channel group (ignored when $targetId is not 0)
     * @param integer $type (0 => templategroup, 1 => regular group, 2 => Query group)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupCopy($scgid, $tsgid, $name, $type = 1)
    {
        $args = array(
            "scgid" => $scgid,
            "name" => $name,
            "tsgid" => $tsgid,
            "type" => $type,
        );

        return $this->query("channelgroupcopy", $args);
    }

    /**
     * Changes the name of a specified channel group.
     *
     * @param integer $cgid
     * @param string $name
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupRename($cgid, $name)
    {
        $args = array(
            "cgid" => $cgid,
            "name" => $name,
        );

        return $this->query("channelgrouprename", $args);
    }

    /**
     * Adds a set of specified permissions to a channel group. Multiple permissions can be added by providing the
     * two parameters of each permission. A permission can be specified by permid or permsid.
     *
     * @param integer $cgid The id of the channel group.
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupAddPerm($cgid, array $permissions)
    {
        $args = $permissions;
        $args["cgid"] = $cgid;

        return $this->query("channelgroupaddperm", $args);
    }

    /**
     * Displays a list of permissions assigned to the channel group specified with $id. If the $permsid option is
     * specified, the output will contain the permission names instead of the internal IDs.
     *
     * @param integer $cgid
     * @param bool $permsid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupPermList($cgid, $permsid = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query("channelgrouppermlist", array("cgid" => $cgid), $options);
    }

    /**
     * Removes a specified permissions from the channel group. A permission can be specified by permid or permsid.
     *
     * @param integer $cgid
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupDelPerm($cgid, array $permissions)
    {
        $args = $permissions;
        $args["cgid"] = $cgid;

        return $this->query("channelgroupdelperm", $args);
    }

    /**
     * Displays all the client and/or channel IDs currently assigned to channel groups. All three parameters are
     * optional so you're free to choose the most suitable combination for your requirements.
     *
     * @param null|integer $channelId
     * @param null|integer $clientDbId
     * @param null|integer $groupId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelGroupClientList($channelId = null, $clientDbId = null, $groupId = null)
    {
        $args = array();
        if (null !== $channelId) {
            $args["cid"] = $channelId;
        }

        if (null !== $clientDbId) {
            $args["cldbid"] = $clientDbId;
        }

        if (null !== $groupId) {
            $args["cgid"] = $groupId;
        }

        return $this->query("channelgroupclientlist", $args);
    }

    /**
     * Sets the channel group of a client to the ID specified with $groupId.
     *
     * @param integer $groupId
     * @param integer $channelId
     * @param integer $clientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function setClientChannelGroup($groupId, $channelId, $clientDbId)
    {
        $args = array(
            "cgid" => $groupId,
            "cid" => $channelId,
            "cldbid" => $clientDbId,
        );

        return $this->query("setclientchannelgroup", $args);
    }

    /**
     * Displays a list of permissions defined for a channel.
     *
     * @param integer $channelId
     * @param bool $permsid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelPermList($channelId, $permsid = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query("channelpermlist", array("cid" => $channelId), $options);
    }

    /**
     * Adds a set of specified permissions to a channel. Multiple permissions can be added by providing the two
     * parameters of each permission. A permission can be specified by permid or permsid.
     *
     * @param integer $channelId
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelAddPerm($channelId, array $permissions)
    {
        $args = $permissions;
        $args["cid"] = $channelId;

        return $this->query("channeladdperm", $args);
    }

    /**
     * Removes a set of specified permissions from a channel. Multiple permissions can be removed at once.
     * A permission can be specified by permid or permsid.
     *
     * @param integer $channelId
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelDelPerm($channelId, array $permissions)
    {
        $args = $permissions;
        $args["cid"] = $channelId;

        return $this->query("channeldelperm", $args);
    }

    /**
     * Displays a list of clients online on a virtual server including their ID, nickname, status flags, etc.
     * The output can be modified using several command options.
     *
     * @param bool $showUid
     * @param bool $showAway
     * @param bool $showVoice
     * @param bool $showTimes
     * @param bool $showGroups
     * @param bool $showInfo
     * @param bool $showIcon
     * @param bool $showCountry
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientList($showUid = false, $showAway = false, $showVoice = false, $showTimes = false, $showGroups = false, $showInfo = false, $showIcon = false, $showCountry = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query("clientlist", array(), $options);
    }

    /**
     * Displays detailed configuration information about a client including unique ID, nickname, client version, etc.
     *
     * @param integer $clientId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientInfo($clientId)
    {
        return $this->query("clientinfo", array("clid" => $clientId));
    }

    /**
     * Displays a list of clients matching a given name pattern.
     *
     * @param string $name
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientFind($name)
    {
        return $this->query("clientfind", array("pattern" => $name));
    }

    /**
     * Changes a clients settings using given properties.
     * For detailed information, see the Client Properties paragraph of the official Teamspeak3 Query documentation.
     *
     * @param integer $clientId
     * @param array $properties
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientEdit($clientId, array $properties)
    {
        $args = array("clid" => $clientId);
        $args = array_merge($args, $properties);

        return $this->query("clientedit", $args);
    }

    /**
     * Displays a list of client identities known by the server including their database ID, last nickname, etc.
     *
     * @param null|integer $start
     * @param null|integer $offset
     * @param bool $showCount
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientDbList($start = null, $offset = null, $showCount = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());
        $args = array();

        if (null !== $start) {
            $args["start"] = $start;
        }

        if (null !== $offset) {
            $args["offset"] = $offset;
        }

        return $this->query("clientdblist", $args, $options);
    }

    /**
     * Displays detailed database information about a client including unique ID, creation date, etc.
     *
     * @param integer $clientDbId The client id.
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientDbInfo($clientDbId)
    {
        return $this->query("clientdbinfo", array("cldbid" => $clientDbId));
    }

    /**
     * Displays a list of client database IDs matching a given pattern. You can either search for a
     * clients last known nickname or his unique identity by using the $showUid option.
     *
     * @param string $pattern
     * @param bool $showUid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientDbFind($pattern, $showUid = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query("clientdbfind", array("pattern" => $pattern), $options);
    }

    /**
     * Changes a clients settings using given properties.
     * For detailed information, see the Client Properties paragraph of the official Teamspeak3 Query documentation.
     *
     * @param integer $clientDbId
     * @param array $properties
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientDbEdit($clientDbId, array $properties)
    {
        $args = array("cldbid" => $clientDbId);
        $args = array_merge($args, $properties);

        return $this->query("clientdbedit", $args);
    }

    /**
     * Deletes a clients properties from the database.
     *
     * @param integer $clientDbId The client db id.
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientDbDelete($clientDbId)
    {
        return $this->query("clientdbdelete", array("cldbid" => $clientDbId));
    }

    /**
     * Displays all client IDs matching the unique identifier specified by $uid.
     *
     * @param string $clientUid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientGetIds($clientUid)
    {
        return $this->query("clientgetids", array("cluid" => $clientUid));
    }

    /**
     * Displays the database ID matching the unique identifier specified by $uid.
     *
     * @param string $clientUid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientGetDbIdFromUid($clientUid)
    {
        return $this->query("clientgetdbidfromuid", array("cluid" => $clientUid));
    }

    /**
     * Displays the database ID and nickname matching the unique identifier specified by $uid.
     *
     * @param string $clientUid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientGetNameFromUid($clientUid)
    {
        return $this->query("clientgetnamefromuid", array("cluid" => $clientUid));
    }

    /**
     * Displays the unique identifier and nickname matching the database ID specified by $clientDbId.
     *
     * @param integer $clientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientGetNameFromDbId($clientDbId)
    {
        return $this->query("clientgetnamefromdbid", array("cldbid" => $clientDbId));
    }

    /**
     * Updates your own ServerQuery login credentials using a specified username. The password will be auto-generated.
     *
     * @param string $username
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientSetServerQueryLogin($username)
    {
        return $this->query("clientsetserverquerylogin", array("client_login_name" => $username));
    }

    /**
     * Change your ServerQuery clients settings using given properties.
     * For detailed information, see the Client Properties paragraph of the official Teamspeak3 Query documentation.
     *
     * @param array $properties
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientUpdate(array $properties)
    {
        return $this->query("clientupdate", $properties);
    }

    /**
     * Moves a client specified with $id to the channel with ID $channelId. If the target channel has
     * a password, it needs to be specified with cpw. If the channel has no password, the parameter can be omitted.
     *
     * @param integer $clientId
     * @param integer $channelId
     * @param null|string $channelPassword
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientMove($clientId, $channelId, $channelPassword = null)
    {
        $args = array(
            "clid" => $clientId,
            "cid" => $channelId,
        );

        if (null !== $channelPassword) {
            $args["cpw"] = $channelPassword;
        }

        return $this->query("clientmove", $args);
    }

    /**
     * Kicks a client specified with $id from their currently joined channel or from the server,
     * depending on $from. The $reson parameter specifies a text message sent to the kicked clients. This
     * parameter is optional and may only have a maximum of 40 characters.
     *
     * @param integer $clientId
     * @param string $reason
     * @param integer $from (4 => channel, 5 => server)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientKick($clientId, $reason = null, $from = 4)
    {
        $args = array(
            "clid" => $clientId,
            "reasonid" => $from,
        );

        if (null !== $reason) {
            $args["reasonmsg"] = substr($reason, 0, 40);
        }

        return $this->query("clientkick", $args);
    }

    /**
     * Sends a poke message to the client specified with $id.
     *
     * @param integer $clientId
     * @param string $message
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientPoke($clientId, $message)
    {
        $args = array(
            "clid" => $clientId,
            "msg" => $message
        );

        return $this->query("clientpoke", $args);
    }

    /**
     * Displays a list of permissions defined for a client.
     *
     * @param integer $clientDbId
     * @param bool $permsid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientPermList($clientDbId, $permsid = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());

        return $this->query("clientpermlist", array("cldbid" => $clientDbId), $options);
    }

    /**
     * Adds a set of specified permissions to a client. Multiple permissions can be added by providing the three
     * parameters of each permission. A permission can be specified by permid or permsid.
     *
     * @param integer $clientDbId
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientAddPerm($clientDbId, array $permissions)
    {
        $args = $permissions;
        $args["cldbid"] = $clientDbId;

        return $this->query("clientaddperm", $args);
    }

    /**
     * Removes a set of specified permissions from a client. Multiple permissions can be removed at once.
     * A permission can be specified by permid or permsid.
     *
     * @param integer $clientDbId
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function clientDelPerm($clientDbId, array $permissions)
    {
        $args = $permissions;
        $args["cldbid"] = $clientDbId;

        return $this->query("clientdelperm", $args);
    }

    /**
     * Displays a list of permissions defined for a client in a specific channel.
     *
     * @param integer $channelId
     * @param integer $clientDbId
     * @param bool $permsid
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelClientPermList($channelId, $clientDbId, $permsid = false)
    {
        $options = $this->getOptions(__METHOD__, func_get_args());
        $args = array(
            "cid" => $channelId,
            "cldbid" => $clientDbId,
        );

        return $this->query("channelclientpermlist", $args, $options);
    }


    /**
     * Adds a set of specified permissions to a client in a specific channel. Multiple permissions can be added
     * by providing the three parameters of each permission. A permission can be specified by permid or permsid.
     *
     * @param integer $channelId
     * @param integer $clientDbId
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelClientAddPerm($channelId, $clientDbId, array $permissions)
    {
        $args = array(
            "cid" => $channelId,
            "cldbid" => $clientDbId,
        );
        $args = array_merge($args, $permissions);

        return $this->query("channelclientaddperm", $args);
    }


    /**
     * Removes a specified permissions from a client in a specific channel.
     * A permission can be specified by permid or permsid.
     *
     * @param integer $channelId
     * @param integer $clientDbId
     * @param array $permissions
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function channelClientDelPerm($channelId, $clientDbId, array $permissions)
    {
        $args = array(
            "cid" => $channelId,
            "cldbid" => $clientDbId,
        );
        $args = array_merge($args, $permissions);

        return $this->query("channelclientdelperm", $args);
    }

    /**
     * Displays a list of permissions available on the server instance including ID, name and description.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function permissionList()
    {
        return $this->query("permissionlist");
    }

    /**
     * Displays the database ID of one or more permissions specified by $name.
     *
     * @param string $name
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function permIdGetByName($name)
    {
        return $this->query("permidgetbyname", array("permsid" => $name));
    }

    /**
     * Displays all permissions assigned to a client for the channel specified with $channelId. If $permId is set to
     * 0, all permissions will be displayed. A permission can be specified by permid or permsid.
     *
     * @param $channelId
     * @param $clientDbId
     * @param integer $permId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function permOverview($channelId, $clientDbId, $permId = 0)
    {
        $args = array(
            "cid" => $channelId,
            "cldbid" => $clientDbId,
            $this->getPermissionKey($permId) => $permId
        );

        return $this->query("permoverview", $args);
    }

    /**
     * Displays the current value of the permission specified with permid or permsid for your own connection.
     * This can be useful when you need to check your own privileges.
     *
     * @param integer|string $permId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function permGet($permId)
    {
        $args = array(
            $this->getPermissionKey($permId) => $permId
        );

        return $this->query("permget", $args);
    }

    /**
     * Displays detailed information about all assignments of the permission specified with $permId.
     * The output is similar to permoverview which includes the type and the ID of the client, channel or
     * group associated with the permission. A permission can be specified by permid or permsid.
     *
     * @param integer|string $permId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function permFind($permId)
    {
        $args = array(
            $this->getPermissionKey($permId) => $permId
        );

        return $this->query("permfind", $args);
    }

    /**
     * Restores the default permission settings on the selected virtual server and creates a new
     * initial administrator token. Please note that in case of an error during the permreset call - e.g. when
     * the database has been modified or corrupted - the virtual server will be deleted from the database.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function permReset()
    {
        return $this->query("permreset");
    }

    /**
     * Displays a list of privilege keys available including their type and group IDs. Tokens can be used to
     * gain access to specified server or channel groups.
     *
     * A privilege key is similar to a client with administrator privileges that adds you to a certain permission
     * group, but without the necessity of a such a client with administrator privileges to actually exist.
     * It is a long (random looking) string that can be used as a ticket into a specific server group.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function privilegeKeyList()
    {
        return $this->query("privilegekeylist");
    }

    /**
     * Create a new token. If tokentype is set to 0, the ID specified with tokenid1 will be a server group ID.
     * Otherwise, tokenid1 is used as a channel group ID and you need to provide a valid channel ID using tokenid2.
     * The tokencustomset parameter allows you to specify a set of custom client properties. This feature can be used
     * when generating tokens to combine a website account database with a TeamSpeak user.
     *
     * @param integer $id
     * @param integer $type (0 => server group, 1 => channel group)
     * @param integer $id2
     * @param null $description
     * @param null $customset
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function privilegeKeyAdd($id, $type = 0, $id2 = 0, $description = null, $customset = null)
    {
        $args = array(
            "tokentype" => $type,
            "tokenid1" => $id,
            "tokenid2" => $id2,
        );

        if (null !== $description) {
            $args["tokendescription"] = $description;
        }

        if (null !== $customset) {
            $args["tokencustomset"] = $customset;
        }

        return $this->query("privilegekeyadd", $args);
    }

    /**
     * Deletes an existing token matching the token key specified with token.
     *
     * @param string $tokenKey
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function privilegeKeyDelete($tokenKey)
    {
        return $this->query("privilegekeydelete", array("token" => $tokenKey));
    }

    /**
     * Use a token key gain access to a server or channel group. Please note that the server will automatically
     * delete the token after it has been used.
     *
     * @param string $tokenKey
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function privilegeKeyUse($tokenKey)
    {
        return $this->query("privilegekeyuse", array("token" => $tokenKey));
    }

    /**
     * Displays a list of offline messages you've received.
     * The output contains the senders unique identifier, the messages subject, etc.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function messageList()
    {
        return $this->query("messagelist");
    }

    /**
     * Sends an offline message to the client specified by $clientUid.
     *
     * @param string $clientUid
     * @param string $subject
     * @param string $message
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function messageAdd($clientUid, $subject, $message)
    {
        $args = array(
            "cluid" => $clientUid,
            "subject" => $subject,
            "message" => $message
        );

        return $this->query("messageadd", $args);
    }

    /**
     * Deletes an existing offline message with ID $id from your inbox.
     *
     * @param integer $messageId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function messageDel($messageId)
    {
        return $this->query("messagedel", array("msgid" => $messageId));
    }

    /**
     * Displays an existing offline message with ID msgid from your inbox. Please note that this does not
     * automatically set the flag_read property of the message.
     *
     * @param integer $messageId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function messageGet($messageId)
    {
        return $this->query("messageget", array("msgid" => $messageId));
    }

    /**
     * Updates the flag_read property of the offline message specified with msgid. If flag is set to 1,
     * the message will be marked as read.
     *
     * @param integer $id
     * @param int $flag (0 => unread, 1 => read)
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function messageUpdateFlag($id, $flag = 0)
    {
        $args = array(
            "msgid" => $id,
            "flag" => $flag,
        );

        return $this->query("messageupdateflag", $args);
    }

    /**
     * Displays a list of complaints on the selected virtual server. If tcldbid is specified, only
     * complaints about the targeted client will be shown.
     *
     * @param integer|null $clientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function complainList($clientDbId = null)
    {
        $args = array();
        if (null !== $clientDbId) {
            $args["tcldbid"] = $clientDbId;
        }

        return $this->query("complainlist", $args);
    }

    /**
     * Submits a complaint about the client with database ID tcldbid to the server.
     *
     * @param integer $clientdbId
     * @param string $message
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function complainAdd($clientdbId, $message)
    {
        $args = array(
            "tcldbid" => $clientdbId,
            "message" => $message,
        );

        return $this->query("complainadd", $args);
    }

    /**
     * Deletes all complaints about the client with database ID $clientDbId from the server.
     *
     * @param integer $clientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function complainDelAll($clientDbId)
    {
        return $this->query("complaindelall", array("tcldbid" => $clientDbId));
    }

    /**
     * Deletes the complaint about the client with ID $toClientDbId submitted by the client with ID $fromClientDbId from the server.
     *
     * @param integer $toClientDbId
     * @param integer $fromClientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function complainDel($toClientDbId, $fromClientDbId)
    {
        $args = array(
            "tcldbid" => $toClientDbId,
            "fcldbid" => $fromClientDbId
        );

        return $this->query("complaindel", $args);
    }

    /**
     * Bans the client specified with ID clid from the server. Please note that this will create two
     * separate ban rules for the targeted clients IP address and his unique identifier.
     *
     * @param integer $clientId
     * @param null $reason
     * @param null $time
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function banClient($clientId, $reason = null, $time = null)
    {
        $args = array("clid" => $clientId);

        if (null !== $reason) {
            $args["banreason"] = $reason;
        }

        if (null !== $time) {
            $args["time"] = (int)$time;
        }

        return $this->query("banclient", $args);
    }

    /**
     * Displays a list of active bans on the selected virtual server.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function banList()
    {
        return $this->query("banlist");
    }

    /**
     * Adds a new ban rule on the selected virtual server. All parameters are optional but at least one of
     * the following must be set: ip, name, or uid.
     *
     * @param null|string $ip
     * @param null|string $name
     * @param null|string $uid
     * @param null|string $reason
     * @param null|integer $time Time in seconds
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function banAdd($ip = null, $name = null, $uid = null, $reason = null, $time = null)
    {
        $args = array();
        if (null !== $ip) {
            $args["ip"] = $ip;
        }

        if (null !== $name) {
            $args["name"] = $name;
        }

        if (null !== $uid) {
            $args["uid"] = $uid;
        }

        if (null !== $reason) {
            $args["banreason"] = $reason;
        }

        if (null !== $time) {
            $args["time"] = (int)$time;
        }

        return $this->query("banadd", $args);
    }

    /**
     * Deletes the ban rule with ID $id from the server.
     *
     * @param int $banId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function banDel($banId)
    {
        return $this->query("bandel", array("banid" => $banId));
    }

    /**
     * Deletes all active ban rules from the server.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function banDelAll()
    {
        return $this->query("bandelall");
    }

    /**
     * Initializes a file transfer upload. $transferId is an arbitrary ID to identify the file transfer on
     * client-side. On success, the server generates a new ftkey which is required to start uploading the file
     * through TeamSpeak 3's file transfer interface.
     *
     * @param string $transferId Arbitrary ID to identify the file transfer on the client-side
     * @param integer $channelId
     * @param string $path
     * @param integer $size
     * @param string $channelPassword
     * @param bool $overwrite
     * @param bool $resume
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftInitUpload($transferId, $channelId, $path, $size, $channelPassword = "", $overwrite = false, $resume = false)
    {
        $args = array(
            "clientftfid" => $transferId,
            "cid" => $channelId,
            "size" => $size,
            "overwrite" => $overwrite,
            "resume" => $resume,
            "cpw" => $channelPassword,
        );

        return $this->query("ftinitupload", $args);
    }

    /**
     * Initializes a file transfer download. clientftfid is an arbitrary ID to identify the file transfer on
     * client-side. On success, the server generates a new ftkey which is required to start downloading the
     * file through TeamSpeak 3's file transfer interface.
     *
     * @param string $transferId Arbitrary ID to identify the file transfer on the client-side
     * @param integer $channelId
     * @param string $path
     * @param string $channelPassword
     * @param int $seekPos
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftInitDowload($transferId, $channelId, $path, $channelPassword = "", $seekPos = 0)
    {
        $args = array(
            "clientftfid" => $transferId,
            "cid" => $channelId,
            "name" => $path,
            "seekpos" => $seekPos,
            "cpw" => $channelPassword,
        );

        return $this->query("ftinitdownload", $args);
    }

    /**
     * Displays a list of running file transfers on the selected virtual server. The output contains the
     * path to which a file is uploaded to, the current transfer rate in bytes per second, etc.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftList()
    {
        return $this->query("ftlist");
    }

    /**
     * Displays a list of files and directories stored in the specified channels file repository.
     *
     * @param integer $channelId
     * @param string $channelPassword
     * @param string $path
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftGetFileList($channelId, $channelPassword = "", $path = "/")
    {
        $args = array(
            "cid" => $channelId,
            "cpw" => $channelPassword,
            "path" => $path,
        );

        return $this->query("ftgetfilelist", $args);
    }

    /**
     * Displays detailed information about one or more specified files stored in a channels file repository.
     *
     * @param integer $channelId
     * @param string $path
     * @param string $channelPassword
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftGetFileInfo($channelId, $path, $channelPassword = "")
    {
        $args = array(
            "cid" => $channelId,
            "cpw" => $channelPassword,
            "name" => $path
        );

        return $this->query("ftgetfileinfo", $args);
    }

    /**
     * Stops the running file transfer with server-side ID serverftfid.
     *
     * @param integer $transferId
     * @param bool $delete
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftStop($transferId, $delete = false)
    {
        $args = array(
            "serverftfid" => $transferId,
            "delete" => $delete
        );

        return $this->query("ftstop", $args);
    }

    /**
     * Deletes one or more files stored in a channels file repository.
     *
     * @param integer $channelId
     * @param string $path
     * @param string $channelPassword
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftDeleteFile($channelId, $path, $channelPassword = "")
    {
        $args = array(
            "cid" => $channelId,
            "cpw" => $channelPassword,
            "name" => $path
        );

        return $this->query("ftdeletefile", $args);
    }

    /**
     * Creates new directory in a channels file repository.
     *
     * @param integer $channelId
     * @param string $name
     * @param string $channelPassword
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftCreateDir($channelId, $name, $channelPassword = "")
    {
        $args = array(
            "cid" => $channelId,
            "dirname" => $name,
            "cpw" => $channelPassword
        );

        return $this->query("ftcreatedir", $args);
    }

    /**
     * Renames a file in a channels file repository. If the two parameters tcid and tcpw are specified,
     * the file will be moved into another channels file repository.
     *
     * @param integer $channelId
     * @param string $oldName
     * @param string $newName
     * @param string $channelPassword
     * @param null $targetChannelId
     * @param string $targetChannelPassword
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function ftRenameFile($channelId, $oldName, $newName, $channelPassword = "", $targetChannelId = null, $targetChannelPassword = "")
    {
        $args = array(
            "cid" => $channelId,
            "oldname" => $oldName,
            "newname" => $newName,
            "cpw" => $channelPassword,
        );

        if (null !== $targetChannelId) {
            $args["tcid"] = $targetChannelId;
            $args["tcpw"] = $targetChannelPassword;
        }

        return $this->query("ftrenamefile", $args);
    }

    /**
     * Searches for custom client properties specified by ident and value. The value parameter can include
     * regular characters and SQL wildcard characters (e.g. %).
     *
     * @param string $ident
     * @param string $pattern
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function customSearch($ident, $pattern)
    {
        $args = array(
            "ident" => $ident,
            "pattern" => $pattern,
        );

        return $this->query("customsearch", $args);
    }

    /**
     * Displays a list of custom properties for the client specified with $clientDbId.
     *
     * @param integer $clientDbId
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function customInfo($clientDbId)
    {
        return $this->query("custominfo", array("cldbid" => $clientDbId));
    }

    /**
     * Displays information about your current ServerQuery connection including your loginname, etc.
     *
     * @return \devmx\Teamspeak3\Query\CommandResponse
     */
    public function whoami()
    {
        return $this->query("whoami");
    }

    /**
     * Retrieve the options set in the given method and arg array.
     *
     * @param string $method
     * @param array $args
     * @return array
     */
    private function getOptions($method, $args)
    {
        $ref = new \ReflectionMethod($method);
        $refArgs = $ref->getParameters();

        $options = array();
        foreach ($args as $index => $arg) {
            $param = $refArgs[$index];
            $paramName = $param->getName();
            //remove the verb in the front, for example: showIcon becomes Icon
            $matched = preg_match("#([A-Z].*?)$#ms", $paramName, $matches);
            //if there was no match, we use the param name directly to get the option name
            $optionName = (isset($matches[0])) ? $matches[0] : $paramName;
            //we only set the option name when the param name is optional, de default value is a boolean
            //and the current iterated argument is true
            if ($param->isOptional() && is_bool($param->getDefaultValue()) && $arg === true) {
                $options[] = strtolower($optionName);
            }
        }

        return $options;
    }

    /**
     * Returns the permission key depending on the value of $permId. If $permId is numeric, the method
     * returns "permid", otherwise "permsid".
     *
     * @param integer|string $permId
     * @return string
     */
    private function getPermissionKey($permId)
    {
        return is_numeric($permId) ? "permid" : "permsid";
    }
    
    /**
     * Returns the names of all commands that only change the state of the query connection 
     * @return array of string  
     */
    public static function getQueryStateChangingCommands() {
        return array(
            'quit',
            'login',
            'logout',
            'use',
            'servernotifyregister',
            'servernotifyunregister',
        );
    }
    
    /**
     * Returns the names of all commands that can change the TeamSpeak3-Server's state
     * @return array of string 
     */
    public static function getServerStateChangingCommands() {
        return array(
            'serverdelete',
            'servercreate',
            'serverstart',
            'serverstop',
            'serverprocessstop',
            'serveredit',
            'servergroupadd',
            'servergroupdel',
            'servergroupcopy',
            'servergrouprename',
            'servergroupaddperm',
            'servergroupdelperm',
            'servergroupaddclient',
            'servergroupdelclient',
            'servergroupautoaddperm',
            'servergroupautodelperm',
            'serversnapshotdeploy',
            'sendtextmessage',
            'logadd',
            'gm',
            'channelmove',
            'channelcreate',
            'channeldelete',
            'channeledit',
            'channelgroupadd',
            'channelgroupdel',
            'channelgroupcopy',
            'channelgroupcopy',
            'channelgrouprename',
            'channelgroupaddperm',
            'channelgroupdelperm',
            'setclientchannelgroup',
            'channeladdperm',
            'channeldelperm',
            'clientedit',
            'clientdbedit',
            'clientdbdelete',
            'clientserverquerylogin',
            'clientupdate',
            'clientmove',
            'clientkick',
            'clientpoke',
            'clientaddperm',
            'clientdelperm',
            'channelclientaddperm',
            'channelclientdelperm',
            'permreset',
            'privilegekeyadd',
            'privilegekeydelete',
            'privilegekeyuse',
            'messageadd',
            'messagedel',
            'messageupdateflag',
            'complainadd',
            'complaindelall',
            'complaindel',
            'banclient',
            'banadd',
            'bandel',
            'bandelall',
            'ftinitupload',
            'ftinitdownload',
            'ftstop',
            'ftdeletefile',
            'ftcreatedir',
            'ftrenamefile',     
        );
    }
    
    /**
     * Returns the names of all commands that do not change anything on the Server or on the Connection
     * @return array of string 
     */
    public static function getNonChangingCommands() {
        return array(
          'help',
          'version',
          'hostinfo',
          'instanceinfo',
          'instanceedit',
          'bindinglist',
          'serverlist',
          'serveridgetbyport',
          'serverinfo',
          'serverrequestconnectioninfo',
          'servergrouplist',
          'servergrouppermlist',
          'servergroupclientlist',
          'servergroupsbyclientid',
          'serversnapshotcreate',
          'logview',
          'channellist',
          'channelinfo',
          'channelfind',
          'channelgrouplist',
          'channelgrouppermlist',
          'channelgroupclientlist',
          'channelpermlist',
          'clientlist',
          'clientinfo',
          'clientfind',
          'clientdblist',
          'clientdbinfo',
          'clientdbfind',
          'clientgetids',
          'clientgetdbidfromuid',
          'clientgetnamefromuid',
          'clientgetnamefromdbid',
          'clientpermlist',
          'channelclientpermlist',
          'permissionslist',
          'permidgetbyname',
          'permoverview',
          'permget',
          'permfind',
          'privilegekeylist',
          'messagelist',
          'messageget',
          'complainlist',
          'banlist',
          'ftlist',
          'ftgetfilelist',
          'ftgetfileinfo',
          'customsearch',
          'custominfo',
          'whoami',          
        );
    }
}
