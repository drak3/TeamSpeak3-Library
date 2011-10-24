<?php
declare(encoding="UTF-8");
namespace devmx\Teamspeak3;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author drak3
 */
interface ServerInterface
{
    
    public function createVirtualServer(VirtualServerInterface $vServerData);
    public function getVirtualServerByPort($port);
    public function getVirtualServerByID($id);
    public function getQueryPort();
    
    public function getUptime();
    public function getTimestampAsUTC();
    
    public function getNumberOfRunningVirtualServers();
    
    public function getSentFileTransferBandwith();
    public function getReceivedFiletransferBandwith();
    
    public function getReceivedPackets();
    public function getSentPackets();
    
    public function getReceivedBytes();
    public function getSentBytes();
    
    public function getBandwithSentLastSecond();
    public function getBandwithReceivedLastSecond();
    
    public function getBandwithSendtLastMinute();
    public function getBandwithReceivedLastMinute();
    
    public function getDatabaseVersion();
    
    public function getQueryGuestGroupID();
    public function getServerAdminGroupTemplateID();
    
    public function getFiletransferPort();
    
    public function getMaxDownloadBandwith();
    public function getMaxUploadBandwith();
    
    public function getDefaultServerGroupTemplateID();
    public function getDefaultChannelGroupTemplateID();
    public function getDefaultChannelAdminGroupTemplateID();
    
    public function getMaxClients();
    public function getNumberOfClients();
    public function getNumberOfChannels();
    
    public function getCommandsAllowedPerFloodtime();
    public function getFloodtime();
    public function getFloodBantime();
    
    public function getBoundIPs();
    
    
    
}

?>
