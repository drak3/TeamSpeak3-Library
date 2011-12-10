<?php


namespace devmx\Teamspeak3\Query\Node;

use devmx\Teamspeak3\Query;
use devmx\Teamspeak3\Node;
use devmx\Teamspeak3\Query\Command;

/**
 * 
 *
 * @author drak3
 */
class Server implements \devmx\Teamspeak3\Node\ServerInterface
{
    /**
     *
     * @var Query\Transport\TransportInterface
     */
    protected $query;
    
    protected $virtualServers = Array();
    
    public function __construct(Query\Transport\TransportInterface $query) {
        $this->query = $query;
        $this->init();
    }
    
    public function init() {
        $this->query->connect();
    }
    
    public function createVirtualServer( VirtualServerInterface $vServerData )
    {
        $args = $this->vServerDataToArguments($vServerData);
       $command = Command::simpleCommand('servercreate', $data);
    }
    
    protected function vServerDataToArguments(Node\VirtualServerInterface $vdata) {
        $data = Array();
        $data['virtualserver_name'] = $vdata->getName();
        $data['virtualserver_port'] = $vdata->getPort();
    }
    
    public function deleteVirtualServer( $identifyer )
    {
        $id = $this->resolveVServerID($identifyer);
        $command = Command::simpleCommand("serverdelete", Array("sid" => $id));
        $response = $this->query->sendCommand($command);
        if(!$response->errorOccured()) {
            if(isset($this->virtualServers[$id])) {
                unset($this->virtualServers[$id]);
            }
        }
        else {
            $response->toException();
        }
    }
    
    /**
     * @param int|\devmx\Teamspeak3\Node\VirtualServerInterface $identifyer
     * @return int
     */
    protected function resolveVServer($identifyer) {
        if($identifyer instanceof Node\VirtualServerInterface) {
            return $identifyer;
        }
        else if(  is_integer( $identifyer) ) {
            return $this->getVirtualServerByID($identifyer);
        }
        else {
            throw new \InvalidArgumentException;
        }
    }

    public function findVirtualServer( $predicate )
    {
        
    }

    public function getBandwidthReceivedLastMinute()
    {
        
    }

    public function getBandwidthReceivedLastSecond()
    {
        
    }

    public function getBandwidthSentLastMinute()
    {
        
    }

    public function getBandwidthSentLastSecond()
    {
        
    }

    public function getBoundIps()
    {
        
    }

    public function getCommandsAllowedPerFloodtime()
    {
        
    }

    public function getCurrentServerTime()
    {
        
    }

    public function getDatabaseVersion()
    {
        
    }

    public function getDefaultChannelAdminGroupTemplateId()
    {
        
    }

    public function getDefaultChannelGroupTemplateId()
    {
        
    }

    public function getDefaultServerGroupTemplateId()
    {
        
    }

    public function getFiletransferPort()
    {
        
    }

    public function getFloodBantime()
    {
        
    }

    public function getFloodtime()
    {
        
    }

    public function getMaxClients()
    {
        
    }

    public function getMaxDownloadBandwidth()
    {
        
    }

    public function getMaxUploadBandwidth()
    {
        
    }

    public function getNumberOfChannels()
    {
        
    }

    public function getNumberOfClients()
    {
        
    }

    public function getNumberOfRunningVirtualServers()
    {
        
    }

    public function getQueryGuestGroupId()
    {
        
    }

    public function getQueryPort()
    {
        
    }

    public function getReceivedBytes()
    {
        
    }

    public function getReceivedFiletransferBandwith()
    {
        
    }

    public function getReceivedPackets()
    {
        
    }

    public function getSentBytes()
    {
        
    }

    public function getSentFileTransferBandwith()
    {
        
    }

    public function getSentPackets()
    {
        
    }

    public function getServerAdminGroupTemplateId()
    {
        
    }

    public function getUptime()
    {
        
    }

    public function getVirtualServerByID( $id )
    {
        
    }

    public function getVirtualServerByPort( $port )
    {
        
    }

    public function getVirtualServers( $predicate = NULL )
    {
        
    }

    public function setBandwidthReceivedLastMinute( $bandwidthReceivedLastMinute )
    {
        
    }

    public function setBandwidthReceivedLastSecond( $bandwidthReceivedLastSecond )
    {
        
    }

    public function setBandwidthSentLastMinute( $bandwidthSentLastMinute )
    {
        
    }

    public function setBandwidthSentLastSecond( $bandwidthSentLastSecond )
    {
        
    }

    public function setBoundIps( $boundIps )
    {
        
    }

    public function setCommandsAllowedPerFloodtime( $commandsAllowedPerFloodtime )
    {
        
    }

    public function setDatabaseVersion( $databaseVersion )
    {
        
    }

    public function setDefaultChannelAdminGroupTemplateId( $defaultChannelAdminGroupTemplateId )
    {
        
    }

    public function setDefaultChannelGroupTemplateId( $defaultChannelGroupTemplateId )
    {
        
    }

    public function setDefaultServerGroupTemplateId( $defaultServerGroupTemplateId )
    {
        
    }

    public function setFiletransferPort( $filetransferPort )
    {
        
    }

    public function setFloodBantime( $floodBantime )
    {
        
    }

    public function setFloodtime( $flootime )
    {
        
    }

    public function setMaxClients( $maxClients )
    {
        
    }

    public function setMaxDownloadBandwidth( $maxDownloadBandwidth )
    {
        
    }

    public function setMaxUploadBandwidth( $maxUploadBandwidth )
    {
        
    }

    public function setNumberOfChannels( $numberOfChannels )
    {
        
    }

    public function setNumberOfClients( $numberOfClients )
    {
        
    }

    public function setNumberOfRunningVirtualServers( $numberOfRunningVirtualServers )
    {
        
    }

    public function setQueryGuestGroupId( $queryGuestGroupId )
    {
        
    }

    public function setQueryPort( $port )
    {
        
    }

    public function setReceivedBytes( $receivedBytes )
    {
        
    }

    public function setReceivedFiletransferBandwith( $receivedFiletransferBandwith )
    {
        
    }

    public function setReceivedPackets( $receivedPackets )
    {
        
    }

    public function setSentBytes( $sentBytes )
    {
        
    }

    public function setSentFileTransferBandwidth( $sentFileTransferBandwidth )
    {
        
    }

    public function setSentPackets( $sentPackets )
    {
        
    }

    public function setServerAdminGroupTemplateId( $serverAdminGroupTemplateId )
    {
        
    }

    public function startVirtualServer( $identifyer )
    {
        
    }

    public function stop()
    {
        
    }

    public function stopVirtualServer( $identifyer )
    {
        
    }

}

?>
