<?php


namespace devmx\Teamspeak3\Query\Node;

/**
 * 
 *
 * @author drak3
 */
class VirtualServer
{
    protected $query;
    protected $data;
    protected $channels;
    protected $clients;
    protected $parent;
    
    protected $clonedQuery = FALSE;
    
    public function __construct(\devmx\Teamspeak3\Query\ServerQuery $query, array $data) {
        $this->query = $query;
        if(!(isset($data['sid']) || isset($data['port']) || isset($data['virtualserver_id']))) {
            throw new \InvalidArgumentException("Identifyer needed");
        }
        if(isset($data['sid'])) {
            $data['virtualserver_id'] = $data['sid'];
            unset($data['sid']);
        }
        $this->data = $data;
    }
    
    public function getChannels() {
        $channellist = $this->query('channellist');
        foreach($channellist->getItems() as $channel) {
            if(isset($channel['cid']) && isset($this->channels[$channel['cid']])) {
                continue;
            }
            else {
                $this->channels[$channel['cid']] = new Channel($query, $this, $channel);
            }
        }
        return $this->channels;
    }
    
    public function createChannel( $data )
    {
        if($data instanceof \devmx\Teamspeak3\Node\ChannelInterface) {
            $this->createChannelWithData($data);
        }
        elseif(is_string($data)) {
            $this->createSimpleChannel($data);
        }
    }
    
    protected function createSimpleChannel($name) {
        $response = $this->query('channelcreate', Array('channel_name' => $name, ));
        $response->toException();
        return $this->addChannel($response->getItem(0));
    }
    
    protected function addChannel($data) {
        if(!isset($data['cid'])) {
            throw new \InvalidArgumentException("Cannot add channel without ID");
        }
        $this->channels[$data['cid']] = new Channel($this->query, $this, $data);
    }
    
    public function getChannelByID($id) {
        if(isset($this->channels[$id])) {
            return $this->channels[$id];
        }
        else {
            $this->getChannels();
            if(isset($this->channels[$id])) {
                return $this->channels[$id];
            }
        }
        return NULL;
    }
    
    /**
     *
     * @param string $command
     * @param array $params
     * @param array $options
     * @return \devmx\Teamspeak3\Query\CommandResponse 
     */
    public function query($command, array $params=Array(), array $options = Array()) {
        $this->switchQueryToServer();
        return $this->query->query($command, $params, $options);
    }
    
    public function registerForEvents() {
        $this->switchQueryToServer();
        $this->query->registerForEvent('server');
        return $this;
    }
    
    public function registerForChatEvents() {
        $this->switchQueryToServer();
        $this->query->registerForEvent('textserver');
        return $this;
    }
    
    public function registerForAllEvents() {
        $this->registerForEvents();
        $this->registerForChatEvents();
        return $this;
    }
    
    public function waitForEvent() {
        return $this->query->waitForEvent();
    }
    
    public function getAllEvents() {
        return $this->query->getAllEvents();
    }
    
    public function createQueryOnServer($force=FALSE) {
        if(!$this->clonedQuery || $force) {
            $this->query = clone $this->query;
            $this->switchQueryToServer();
            $this->clonedQuery = TRUE;
        }
    }
    
    public function switchQueryToServer($force=FALSE) {
        $changeNeeded = TRUE;
        if($this->query->isOnVirtualServer()) {
            $ident = $this->query->getVirtualServerIdentifyer();
            if(isset($ident['sid']) && isset($this->data['virtualserver_id']) && $this->data['virtualserver_id'] == $ident['sid']) {
                $changeNeeded = FALSE;
            }
            if(isset($ident['port']) && isset($this->data['port']) && $this->data['port'] == $ident['port']) {
                $changeNeeded = FALSE;
            }
            if(isset($this->data['port']) && $this->query->getVirtualServerPort() == $this->data['port']) {
                $changeNeeded = FALSE;
            }
            if(isset($this->data['virtualserver_id']) && $this->query->getVirtualServerID() == $this->data['virtualserver_id']) {
                $changeNeeded = FALSE;
            }
        }
        if(($changeNeeded  && !$this->clonedQuery) || $force) {
            if(isset($this->data['virtualserver_id'])) {
                $this->query->query('use', Array('sid'=>$this->data['virtualserver_id']), Array('virtual'))->toException();
            }
            elseif(isset($this->data['port'])) {
                $this->query->query('use', Array('port'=>$this->data['port']),Array('virtual'))->toException();
            }
            else {
                throw new \RuntimeException("cannot switch to virtual server");
            }
        }
    }
    
}

?>
