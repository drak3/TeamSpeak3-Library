<?php


namespace devmx\Teamspeak3;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 *
 * @author drak3
 */
class VirtualServer implements \devmx\Teamspeak3\Node\VirtualServerInterface
{
    public function createChannel($channelData);
    public function getChannelByID($id);
    public function findChannelsByName($name);
    public function findChannels($predicate); 
    
    public function getClients($predicate=NULL);
    public function getClientById($id);
    public function getClientByDBID($id);
    public function findClients($predicate);
    public function createClient($data);
    
    public function sendMessage($msg);
    
    public function addToken($token);
    public function getTokens();
    
    public function getName()
    {
        return $this["virtualserver_name"];
    }
    
    public function setName($name)
    {
        $this['virtualserver_name'] = $name;
        return $this;
    }
    
    
    public function getWelcomeMessage()
    {
        return $this["virtualserver_welcomemessage"];
    }
    
    public function setWelcomeMessage($welcomeMessage)
    {
        $this["virutalserver_welcomemessage"] = $welcomeMessage ;
        return $this;
    }
    
    
    public function getMaximumClients()
    {
        return $this["virtualserver_maxclients"];
    }
    
    public function setMaximumClients($maxClients)
    {
        $this["virtualserver_maxclients"] = $maxClients;
        return $this;
    }
    
    
    public function hasPassword()
    {
        return $this["VIRTUALSERVER_FLAG_PASSWORD"];
    }
    
    public function getPassword()
    {
        return $this["virtualserver_password"];
    }
    
    public function setPassword($pass)
    {
        $this["virtualserver_password"] = $pass;
        return $this;
    }
    
    
    public function getNumberOfClients()
    {
        return $this["virtualserver_clientsonline"];
    }
    
    
    public function getNumberOfQueryClients()
    {
        return $this["virtualserver_queryclients_online"];
    }
    
    
    public function getNumberOfChannels()
    {
        return $this["virtualserver_channelsonline"];
    }
    
    
    public function getDateOfCreation()
    {
        return $this["virtualserver_created"];
    }
    
    public function setDateOfCreation(\DateTime $creation)
    {
        $this["virtualserver_created"] = $creation;
        return $this;
    }
    
    
    public function getUptime()
    {
        return $this["virtualserver_uptime"];
    }
    
    public function setUptime(\DateInterval $uptime)
    {
        $this["virtualserver_uptime"] = $uptime;
        return $this;
    }
    
    
    public function getHostMessage()
    {
        return $this["virtualserver_hostmessage"];
    }
    
    public function setHostMessage($msg)
    {
        $this["virtualserver_hostmessage"] = $msg;
        return $this;
    }
    
    
    public function getHostMessageMode()
    {
        return $this["virtualserver_hostmessage_mode"];
    }
    
    public function setHostMessageMode($mode)
    {
        $this["virtualserver_hostmessage_mode"] = $mode;
        return $this;
    }
    
    
    public function getDefaultChannelGroup()
    {
        return $this["virtualserver_default_channel_group"];
    }
    
    public function setDefaultChannelGroup($group)
    {
        $this["virtualserver_default_channel_group"];
        return $this;
    }
    
    
    public function getDefaultServerGroup()
    {
        $this["virtualserver_default_server_group"];
    }
    
    public function setDefaultServerGroup($group)
    {
        $this["virtualserver_default_server_group"] = $group;
        return $this;    
    }
    
    
    public function getDefaultChannelAdminGroup()
    {
        return $this["virtualserver_default_channel_admin_group"];
    }
    
    public function setDefaultChannelAdminGroup($group)
    {
        $this["virtualserver_default_channel_admin_group"] = $group;
        return $this;
    }
    
    
    public function getPlatform()
    {
        return $this["virtualserver_platform"];
    }
    
    public function setPlatform($platform)
    {
        $this["virtualserver_platform"] = $platform;
        return $this;
    }
    
    
    public function getVersion()
    {
        return $this["virtualserver_version"];
    }
    
    public function setVersion(\devmx\Teamspeak3\Version $version)
    {
        $this["virtualserver_version"] = $version;
        return $this;
    }
    
    
    public function getMaximumDownloadBandwidth()
    {
        return $this["virtualserver_max_download_total_bandwidth"];
    }
    
    public function setMaximumDownloadBandwidth($bandwidth)
    {
        $this["virtualserver_max_download_total_bandwidth"] = $bandwidth;
        return $this;
    }
    
    
    public function getMaximumUploadBandwidth()
    {
        return $this["virtualserver_max_upload_total_bandwidth"];
    }
    
    public function setMaximumUploadBandwidth($bandwidth)
    {
        $this["virtualserver_max_upload_total_bandwidth"] = $bandwidth;
        return $this;
    }
    
    
    public function getHostBannerURL()
    {
        return $this["virtualserver_hostbanner_url"];
    }
    
    public function setHostBannerURL($url)
    {
        $this["virtualserver_hostbanner_url"] = $url;
        return $this;
    }
    
    
    public function getHostBannerGFXURL()
    {
        return $this["virtualserver_hostbanner_gfx_url"];
    }
    
    public function setHostBannerGFXURL($url)
    {
        $this["virtualserver_hostbanner_gfx_url"] = $url;
        return $this;
    }
    
    
    public function getHostBannerReloadInterval()
    {
        return $this["virtualserver_hostname_gfx_interval"];
    }
    
    public function setHostBannerReloadInterval(\DateInterval $interval)
    {
        $this["virtualserver_hostname_gfs_interval"];
        return $this;
    }
    
    
    public function getComplainLimit()
    {
        return $this["virtualserver_complain_autoban_count"];
    }
    
    public function setComplainLimit($limit)
    {
        $this["virtualserver_complain_autoban_count"] = $limit;
        return $this;
    }
    
    
    public function getAutoBanTime()
    {
        return $this["virtualserver_complain_autoban_time"];
    }
    
    public function setAutoBanTime(\DateInterval $time)
    {
        $this["virtualserver_complain_autoban_time"] = $time;
        return $this;
    }
    
    
    public function getComplainRemoveTime()
    {
        return $this["virtualserver_complain_remove_time"];
    }
    
    public function setComplainRemoveTime(\DateInterval $time)
    {
        $this["virtualserver_complain_remove_time"] = $time;
        return $this;
    }
    
    
    public function getNumberOfClientsInChannelBeforeForcedSilence()
    {
        return $this["VIRTUALSERVER_MIN_CLIENTS_IN_CHANNEL_BEFORE_FORCED_SILENCE"];
    }
    
    
    public function setNumberOfClientsInChannelBeforeForcedSilence($number)
    {
        $this["VIRTUALSERVER_MIN_CLIENTS_IN_CHANNEL_BEFORE_FORCED_SILENCE"] = $number;
        return $this;
    }
    
    
    public function getPrioritySpeakerDimmModificator()
    {
        return $this["VIRTUALSERVER_PRIORITY_SPEAKER_DIMM_MODIFICATOR"];
    }
    
    public function setPrioritySpeakerDimmModificatior($mod)
    {
        $this["VIRTUALSERVER_PRIORITY_SPEAKER_DIMM_MODIFICATOR"] = $mod;
        return $this;
    }
    
    
    public function getAntiFloodPointsReducedByTick()
    {
        return $this["VIRTUALSERVER_ANTIFLOOD_POINTS_TICK_REDUCE"];
    }
    
    public function setAntiFloodPointsReducedByTick($points)
    {
        $this["VIRTUALSERVER_ANTIFLOOD_POINTS_TICK_REDUCE"] = $points;
        return $this;
    }
    
    
    public function getAntiFloodPointsNeededForWarning()
    {
        return $this["VIRTUALSERVER_ANTIFLOOD_POINTS_NEEDED_WARNING"];
    }
    
    public function setAntiFloodPointsNeededForWarning($points)
    {
        $this["VIRTUALSERVER_ANTIFLOOD_POINTS_NEEDED_WARNING"] = $points;
        return $this;
    }
    
    
    public function getAntiFloodPointsNeededForBan()
    {
        return $this["VIRTUALSERVER_ANTIFLOOD_POINTS_NEEDED_BAN"];
    }
    
    public function setAntiFloodPointsNeededForBan($points)
    {
        $this["VIRTUALSERVER_ANTIFLOOD_POINTS_NEEDED_BAN"] = $points;
        return $this;
    }
    
    
    public function getAutomaticBanTime()
    {
        return $this["VIRTUALSERVER_ANTIFLOOD_POINTS _BAN_TIME"];
    }
    
    public function setAutomaticBanTime(\DateInterval $time)
    {
        $this["VIRTUALSERVER_ANTIFLOOD_POINTS _BAN_TIME"] = $time;
        return $this;
    }
    
    
    public function getClientConnections()
    {
        return $this["VIRTUALSERVER_CLIENT_CONNECTIONS"];
    }
    
    public function setClientConnections($cons)
    {
        $this["VIRTUALSERVER_CLIENT_CONNECTIONS"] = $cons;
        return $this;
    }
    
    
    public function getQueryClientConnections()
    {
        return $this["VIRTUALSERVER_QUERY_CLIENT_CONNECTIONS"];
    }
    
    public function setQueryClientConnections($cons)
    {
        $this["VIRTUALSERVER_QUERY_CLIENT_CONNECTIONS"] = $this;
        return $this;
    }
    
    
    public function getHostButtonTooltipText()
    {
        return $this["VIRTUALSERVER_HOSTBUTTON_TOOLTIP"];
    }
    
    public function setHostButtonTooltipText($text)
    {
        $this["VIRTUALSERVER_HOSTBUTTON_TOOLTIP"] = $text;
        return $this;
    }
    
    
    public function getHostButtonGFXURL()
    {
        return $this["VIRTUALSERVER_HOSTBUTTON_GFX_URL"];
    }
    
    public function setHostButtonGFXURL($url)
    {
        $this["VIRTUALSERVER_HOSTBUTTON_GFX_URL"] = $url;
        return $this;
    }
    
    
    public function getHostButtonURL()
    {
        return $this["VIRTUALSERVER_HOSTBUTTON_URL"];
    }
    
    public function setHostButtonURL($url)
    {
        $this["VIRTUALSERVER_HOSTBUTTON_URL"] = $url;
        return $this;
    }
    
    
    public function getDownloadQuota()
    {
       return $this["VIRTUALSERVER_DOWNLOAD_QUOTA"];
    }
    
    public function setDownloadQuota($quota)
    {
        $this["VIRTUALSERVER_DOWNLOAD_QUOTA"] = $quota;
        return $this;
    }
    
    
    public function getUploadQuota()
    {
        return $this["VIRTUALSERVER_UPLOAD_QUOTA"];
    }
    
    public function setUploadQuota($quota)
    {
        $this["VIRTUALSERVER_UPLOAD_QUOTA"] = $quota;
        return $this;
    }
    
    
    public function getBytesUploadedThisMonth()
    {
        return $this["VIRTUALSERVER_MONTH_BYTES_DOWNLOADED"];
    }
    
    public function setBytesUploadedThisMonth($bytes)
    {
        $this["VIRTUALSERVER_MONTH_BYTES_DOWNLOADED"] = $bytes;
        return $this;        
    }
    
    
    public function getBytesDownloadedThisMonth()
    {
        return $this["VIRTUALSERVER_MONTH_BYTES_UPLOADED"];
    }
    
    public function setBytesDownloadedThisMonth($bytes)
    {
        $this["VIRTUALSERVER_MONTH_BYTES_UPLOADED"] = $bytes;
        return $this;
    }
    
    
    public function getBytesDownloaded()
    {
        return $this["VIRTUALSERVER_TOTAL_BYTES_DOWNLOADED"];
    }
    
    public function setBytesDownloaded($bytes)
    {
        $this["VIRTUALSERVER_TOTAL_BYTES_DOWNLOADED"] = $bytes;
        return $this;
    }
    
    
    public function getBytesUploaded()
    {
        return $this["VIRTUALSERVER_ TOTAL_BYTES_UPLOADED"];
    }
    
    public function setBytesUploaded($bytes)
    {
        $this["VIRTUALSERVER_ TOTAL_BYTES_UPLOADED"] = $bytes;
        return $this;
    }
    
    
    public function getUniqueID()
    {
        return $this["VIRTUALSERVER_UNIQUE_IDENTIFER"];
    }
    
    public function setUniqueID($id)
    {
        $this["VIRTUALSERVER_UNIQUE_IDENTIFER"] = $id;
        return $this;
    }
    
    
    public function getID()
    {
        $this["VIRTUALSERVER_ID"];
    }
    
    public function setID($id)
    {
        $this["VIRTUALSERVER_ID"] = $id;
        return $this;
    }
    
    
    public function getMachineID()
    {
        return $this["VIRTUALSERVER_MACHINE_ID"];
    }
        
    public function setMachineID($id)
    {
        $this["VIRTUALSERVER_MACHINE_ID"] = $id;
        return $this;
    }
    
    
    public function getPort()
    {
        return $this["VIRTUALSERVER_PORT"];
    }
    
    public function setPort($port)
    {
        $this["VIRTUALSERVER_PORT"] = $port;
        return $this;
    }
    
    
    public function isAutostarting()
    {
        return $this["VIRTUALSERVER_AUTOSTART"];
    }
    
    public function setIsAutostarting($isAutoStarting)
    {
        $this["VIRTUALSERVER_AUTOSTART"] = $isAutoStarting;
        return $this;
    }
    
    
    public function getFileTransferBandwidthSent()
    {
        return $this["CONNECTION_FILETRANSFER_BANDWIDTH_SENT"];
    }
    
    public function setFileTransferBandwidthSent($bandwidth)
    {
        $this["CONNECTION_FILETRANSFER_BANDWIDTH_SENT"] = $bandwidth;
        return $this;
    }
    
    
    public function getFileTransferBandwidthReceived()
    {
        return $this["CONNECTION_FILETRANSFER_BANDWIDTH_RECEIVED"];
    }
    
    public function setFileTransferBandwidthReceived($bandwidth)
    {
        $this["CONNECTION_FILETRANSFER_BANDWIDTH_RECEIVED"] = $bandwidth;
        return $this;
    }
    
    
    public function getPacketsSent()
    {
        return $this["CONNECTION_PACKETS_SENT_TOTAL"];
    }
    
    public function setPacketsSent($packets)
    {
        $this["CONNECTION_PACKETS_SENT_TOTAL"] = $packets;
        return $this;
    }
    
    
    public function getPacketsReceived()
    {
        return $this["CONNECTION_PACKETS_RECEIVED_TOTAL"];
    }
    
    public function setPacketsReceived($packets)
    {
        $this["CONNECTION_PACKETS_RECEIVED_TOTAL"] = $packets;
        return $this;
    }
    
    
    public function getBytesSent()
    {
        return $this["CONNECTION_BYTES_SENT_TOTAL"];
    }
    
    public function setBytesSent($bytes)
    {
        $this["CONNECTION_BYTES_SENT_TOTAL"] = $bytes;
        return $this;
    }
   
  
    public function getBytesReceived()
    {
        return $this["CONNECTION_BYTES_RECEIVED_TOTAL"];
    }
    
    public function setBytesReceived($bytes)
    {
        $this["CONNECTION_BYTES_RECEIVED_TOTAL"] = $bytes;
        return $this;
    }
    
    
    public function getBandwidthSentLastSecond()
    {
        return $this["CONNECTION_BANDWIDTH_SENT_LAST_SECOND_TOTAL"];
    }
    
    public function setBandwidthSentLastSecond($bandwidth)
    {
        $this["CONNECTION_BANDWIDTH_SENT_LAST_SECOND_TOTAL"] = $bandwidth;
        return $this;
    }
    
    
    public function getBandwidthReceivedLastSecond()
    {
        return $this["CONNECTION_BANDWIDTH_RECEIVED_LAST_SECOND_TOTAL"];
    }
    
    public function setBandwidthReceivedLastSecond($bandwidth)
    {
        $this["CONNECTION_BANDWIDTH_RECEIVED_LAST_SECOND_TOTAL"] = $bandwidth;
        return $this;
    }
    
    
    public function getBandwidthSentLastMinute()
    {
        return $this["CONNECTION_BANDWIDTH_SENT_LAST_MINUTE_TOTAL"];
    }
    
    public function setBandwidthSentLastMinute($bandwidth)
    {
        $this["CONNECTION_BANDWIDTH_SENT_LAST_MINUTE_TOTAL"] = $bandwidth;
    }
    
    
    public function getBandwidthReceivedLastMinute()
    {
        return $this["CONNECTION_BANDWIDTH_RECEIVED_LAST_MINUTE_TOTAL"];
    }
    
    public function setBandwidthReceivedLastMinute($bandwidth)
    {
        $this["CONNECTION_BANDWIDTH_RECEIVED_LAST_MINUTE_TOTAL"] = $bandwidth;
        return $this;
    }
    
    
    public function getStatus()
    {
        return $this["VIRTUALSERVER_STATUS"];
    }
    
    public function setStatus($status)
    {
        $this["VIRTUALSERVER_STATUS"] = $status;
        return $this;
    }
    
    
    public function logsClientEvents()
    {
        return $this["VIRTUALSERVER_ LOG_CLIENT"];
    }
    
    public function setLogsClientEvents($logs)
    {
        $this["VIRTUALSERVER_ LOG_CLIENT"] = $logs;
        return $this;
    }
    
    
    public function logsQueryEvents()
    {
        return $this["VIRTUALSERVER_ LOG_QUERY"];
    }
    
    public function setLogsQueryEvents($logs)
    {
        $this["VIRTUALSERVER_ LOG_QUERY"] = $logs;
        return $this;
    }
    
    
    public function logsChannelEvents()
    {
        return $this["VIRTUALSERVER_ LOG_CHANNEL"];
    }
    
    public function setLogsChannelEvents($logs)
    {
        $this["VIRTUALSERVER_ LOG_CHANNEL"] = $logs;
        return $this;
    }
    
    
    public function logsPermissionEvents()
    {
        return $this["VIRTUALSERVER_ LOG_PERMISSIONS"];
    }
    
    public function setLogsPermissionEvents($logs)
    {
        $this["VIRTUALSERVER_ LOG_PERMISSIONS"] = $logs;
        return $this;
    }
    
    
    public function logsServerEvents()
    {
        return $this["VIRTUALSERVER_ LOG_SERVER"];
    }
    
    public function setLogsServerEvents($logs)
    {
        $this["VIRTUALSERVER_ LOG_SERVER"] = $logs;
        return $this;
    }
    
    
    public function logsFileTransferEvents()
    {
        return $this["VIRTUALSERVER_ LOG_FILETRANSFER"];
    }
    
    public function setLogsFileTransferEvents($logs)
    {
        $this["VIRTUALSERVER_ LOG_FILETRANSFER"] = $logs;
        return $this;
    }
    
    
    public function getMinimalClientVersion()
    {
        return $this["VIRTUALSERVER_MIN_CLIENT_VERSION"];
    }
    
    public function setMinimalClientVersion(Version $version)
    {
        $this["VIRTUALSERVER_MIN_CLIENT_VERSION"] = $version;
        return $this;
    }
    
    
    public function getNeededSecurityLevel()
    {
        return $this["VIRTUALSERVER_NEEDED_IDENTITY_SECURITY_LEVEL"];
    }
    
    public function setNeededSecurityLevel($level)
    {
        $this["VIRTUALSERVER_NEEDED_IDENTITY_SECURITY_LEVEL"] = $level;
        return $this;
    }
    
    
    public function getPhoneticName()
    {
        return $this["VIRTUALSERVER_NAME_PHONETIC"];
    }
    
    public function setPhoneticName($name)
    {
        $this["VIRTUALSERVER_NAME_PHONETIC"] = $name;
        return $this;
    }
    
    
    public function getIconID()
    {
        return $this["VIRTUALSERVER_ICON_ID"];
    }
    
    public function setIconID($id)
    {
        $this["VIRTUALSERVER_ICON_ID"] = $id;
        return $this;
    }
    
    
    public function getReservedSlots()
    {
        return $this["VIRTUALSERVER_RESERVED_SLOTS"];
    }
    
    public function setReservedSlots($slots)
    {
        $this["VIRTUALSERVER_RESERVED_SLOTS"] = $slots;
        return $this;
    }
    
    public function getSpeechPacketLoss()
    {
        return $this["VIRTUALSERVER_TOTAL_PACKETLOSS_SPEECH"];
    }
    
    public function setSpeechPacketLoss($loss)
    {
        $this["VIRTUALSERVER_TOTAL_PACKETLOSS_SPEECH"] = $loss;
        return $this;
    }
    
    
    public function getKeepalivePacketLoss()
    {
        return $this["VIRTUALSERVER_TOTAL_PACKETLOSS_KEEPALIVE"];
    }
    
    public function setKeepalivePacketLoss($loss)
    {
        $this["VIRTUALSERVER_TOTAL_PACKETLOSS_KEEPALIVE"] = $loss;
        return $this;
    }
    
    
    public function getControlPacketLoss()
    {
        return $this["VIRTUALSERVER_TOTAL_PACKETLOSS_CONTROL"];
    }
    
    public function setControlPacketLoss($loss)
    {
        $this["VIRTUALSERVER_TOTAL_PACKETLOSS_CONTROL"] = $loss;
        return $this;
    }
    
    
    public function getTotalPacketLoss()
    {
        return $this["VIRTUALSERVER_TOTAL_PACKETLOSS_TOTAL"];
    }
    
    public function setTotalPacketLoss($loss)
    {
        $this["VIRTUALSERVER_TOTAL_PACKETLOSS_TOTAL"] = $loss;
        return $this;
    }

    
    public function getAverageClientPing()
    {
        return $this["VIRTUALSERVER_TOTAL_PING"];
    }
    
    public function setAverageClientPing($ping)
    {
        $this["VIRTUALSERVER_TOTAL_PING"] = $ping;
        return $this;
    }
    
    
    public function getIp()
    {
        return $this["VIRTUALSERVER_IP"];
    }
    
    public function setIp($ip)
    {
        $this["VIRTUALSERVER_IP"] = $ip;
        return $this;
    }
    
    
    public function hasWebListEnabled()
    {
        return $this["VIRTUALSERVER_WEBLIST_ENABLED"];
    }
    
    public function setHasWebListEnabled($has)
    {
        $this["VIRTUALSERVER_WEBLIST_ENABLED"] = $has;
        return $this;
    }
    
    
    public function getCodecEncryptionMode()
    {
        return $this["VIRTUALSERVER_CODEC_ENCRYPTION_MODE"];
    }
    
    public function setCodecEncryptionMode($encription)
    {
        $this["VIRTUALSERVER_CODEC_ENCRYPTION_MODE"] = $encription;
        return $this;
    }
    
    
    public function getFileBase()
    {
        return $this["VIRTUALSERVER_FILEBASE"];
    }
    
    public function setFileBase($base)
    {
        $this["VIRTUALSERVER_FILEBASE"] = $base;
        return $this;
    }
}

?>
