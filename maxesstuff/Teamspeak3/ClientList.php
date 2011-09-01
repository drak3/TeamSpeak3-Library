<?php

declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3;

/**
 * @author drak3
 */
class ClientList implements \ArrayAccess, \Iterator, \Countable
{
    protected $query;
    protected $clientListResponse;
    protected $clients;
    
    public function __construct(Query\QueryTransport $query, array $clientlListResponse = Array(), array $clients = Array()) {
        $this->query = $query;
        $this->clientList= $clientListResponse;
        $this->clients = $clients;
        foreach($clientListResponse as $clientResponse) {
            $client = new Client($clientResponse);
            $clients[$client->getId()] = $client;
        }
    }
    
    public function getClient($clid) {
        return $this->clients[$clid];
    }
    
    public function find($predicate) {
        if(!is_callable( $predicate) )
            throw new \InvalidArgumentException("predicate must be callable");
        else {
            foreach($this->clients as $client) {
                if($predicate($client))
                    return $client;
            }
        }
    }
    
    /**
     *
     * @param callable $predicate a callable which maps a boolean to a Client
     * @return ClientList a clientlist with all clients matching $predicate 
     */
    public function filter($predicate) {
        $matching = Array();
        if(!is_callable( $predicate) )
            throw new \InvalidArgumentException("predicate must be callable");
        else {
            foreach($this->clients as $client) {
                if($predicate($client))
                    $matching[$client->getId] = $client;
            }
        }
        return new ClientList($this->query, Array(), $matching);
    }
    
    public function getClientByName($name) {
        return $this->find(function($client) use ($name) { return ($client->getNickname() == $name);} );
    }
    
    public function getClientByUniqueIdentifyer($ident) {
        return $this->find(function($client) use ($ident) { return ($client->getUniqueIdentifier() == $name);} );
    }
    
}

?>
