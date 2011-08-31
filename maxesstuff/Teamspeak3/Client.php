<?php
namespace maxesstuff\Teamspeak3;
use \maxesstuff\Teamspeak3\Query;

class Client implements \ArrayAccess
{    
   
    public function __construct(QueryTransport $query, $properties=Array())
    {
        $this->query = $query;
        $this->properties = $properties;
    }
    
    public function getProperty($name,$else=NULL) {
        if(isset($this->properties[$name])) {
            return $this->properties[$name];
        }
        else {
            if(!$this->loadedClientInfo) {
              $this->loadClientInfo();
              return $this->getProperty($name,$else);
            }    
            else
                return $else;
        }
    }
    
    protected function loadClientInfo() {
        $cmd = Query\Command::simpleCommand("clientinfo", Array("clid"=>$this->properties['clid']));
        $response = $this->query->sendCommand($cmd);
        if($response['response']->getErrorID() != 0)
            throw new RuntimeError("Loading of clientlist failed with errorid ".$response['response']->getErrorID()." and errormessage".$response['response']->getErrorMessage());
        else {
            $this->properties = $response['response']->getItems();
            if(  is_array( $this->properties) )
                    $this->properties = $this->properties[0];
        }       
    }
    
    public function offsetExists( $offset )
    {
        return isset($this->properties[$offest]);
    }

    public function offsetGet( $offset )
    {
        return $this->getProperty($offset);
    }

    public function offsetSet( $offset , $value ) {}

    public function offsetUnset( $offset ) {}

    
    public function getCid()
    {
        return $this->getProperty("cid");
    }

    public function getIdleTime()
    {
        return $this->getProperty("client_idle_time");
    }

    public function getUniqueIdentifier()
    {
        return $this->getProperty("client_unique_identifyer");
    }

    public function getNickname()
    {
        return $this->getProperty("client_nickname");
    }

    public function getVersion()
    {
        return $this->getProperty("client_version");
    }

    public function getPlatform()
    {
        return $this->getProperty("client_platform");
    }

    public function getInputMuted()
    {
        return $this->getProperty("client_input_muted");
    }

    public function getOutputMuted()
    {
        return $this->getProperty("client_output_muted");
    }

    public function getOutputonlyMuted()
    {
        return $this->getProperty("client_outputonly_muted");
    }

    public function getInputHardware()
    {
        return $this->getProperty("client_input_hardware");
    }

    public function getOutputHardware()
    {
        return $this->getProperty( "client_output_hardware");
    }

    public function getDefaultChannel()
    {
        return $this->getProperty("client_default_channel");
    }

    public function getMetaData()
    {
        return $this->getProperty("client_meta_data");
    }

    public function IsRecording()
    {
        return $this->getProperty( "client_is_recording");
    }

    public function getLogiNname()
    {
        return $this->getProperty( "client_login_name");
    }

    public function getDatabaseId()
    {
        return $this->getProperty("client_database_id");
    }

    public function getChannelGroupId()
    {
        return $this->getProperty("client_database_id");
    }

    public function getServergroups()
    {
        return $this->getProperty("client_servergroups");
    }

    public function getCreated()
    {
        return $this->getProperty( "client_created");
    }

    public function getLastConnected()
    {
        return $this->geProperty("client_last_connected");
    }

    public function getTotalConnections()
    {
        return $this->getProperty( "client_total_connections");
    }

    public function getAway()
    {
        return $this->getProperty("client_away");
    }

    public function getAwayMessage()
    {
        return $this->getProperty("client_away_message");
    }

    public function getType()
    {
        return $this->getProperty("client_type");
    }

    public function getFlagAvatar()
    {
        return $this->getProperty("client_flag_avatar");
    }

    public function getTalkPower()
    {
        return $this->getProperty( "client_talkpower");
    }

    public function getTalkRequest()
    {
        return $this->getProperty( "client_talk_request");
    }

    public function getTalkRequestMsg()
    {
        return $this->getProperty("client_talk_request_msg");
    }

    public function getDescription()
    {
        return $this['client_description'];
    }

    public function IsTalker()
    {
        return $this['client_is_talker'];
    }

    public function getMonthBytesUploaded()
    {
        return $this['client_month_bytes_uploaded'];
    }

    public function getMonthBytesDownloaded()
    {
        return $this['client_month_bytes_downloaded'];
    }

    public function getTotalBytesUploaded()
    {
        return $this['client_total_bytes_uploaded'];
    }

    public function getTotalBytesDownloaded()
    {
        return $this['client_total_bytes_downloaded'];
    }

    public function IsPrioritySpeaker()
    {
        return $this['client_is_priority_speaker'];
    }

    public function getNicknamePhonetic()
    {
        return $this['client_nickname_phonetic'];
    }

    public function getNeededServerQueryViewPower()
    {
        return $this['client_needed_server_query_view_power'];
    }

    public function getDefaultToken()
    {
        return $this['client_default_token'];
    }

    public function getIconId()
    {
        return $this['client_icon_id'];
    }

    public function IsChannelCommander()
    {
        return $this['client_is_channel_commander'];
    }

    public function getCountry()
    {
        return $this['client_country'];
    }

    public function getChannelGroupInheritedChannelId()
    {
        return $this['client_channel_group_inherited_channel_id'];
    }

    public function getBase64HashClientUID()
    {
        return $this['client_base64_client_UID'];
    }

    public function getConnectionFiletransferbandwidthSent()
    {
        return $this['connection_filetransferbandwidth_sent'];
    }

    public function getConnectionFiletransferbandwidthReceived()
    {
        return $this['connection_filetransferbandwidth_received'];
    }

    public function getConnectionPacketssentTotal()
    {
        return $this['connection_packetssent_total'];
    }

    public function getConnectionBytessentTotal()
    {
        return $this['connection_bytessent_total'];
    }

    public function getConnectionPacketsreceivedTotal()
    {
        return $this['connection_packetsreceived_total'];
    }

    public function getConnectionBytesreceivedTotal()
    {
        return $this['connection_bytesreceived_total'];
    }

    public function getConnectionBandwidthSentLastSecondTotal()
    {
        return $this['connection_bandwith_sent_last_second_total'];
    }

    public function getConnectionBandwidthSentlastMinuteTotal()
    {
        return $this['conncection_bandwidth_sent_last_minute_total'];
    }

    public function getConnectionBandwidthReceivedLastSecondTotal()
    {
        return $this['connection_bandwidth_received_last_second_total'];
    }

    public function getConnectionBandwidthReceivedLastMinuteTotal()
    {
        return $this['connection_bandwidth_received_last_minute_total'];
    }

    public function getConnectionConnectedTime()
    {
        return $this["connection_connected_time"];
    }

    public function getConnectionClientIp()
    {
        return $this["connection_client_ip"];
    }


}

?>