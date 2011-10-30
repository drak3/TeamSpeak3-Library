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
