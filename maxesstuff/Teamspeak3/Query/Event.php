<?php
declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query;

/**
 * 
 *
 * @author drak3
 */
class Event extends Response
{
    protected $reason;
    
    public function __construct($reason, array $items) {
        $this->reason = $reason;
        $this->items = $items;            
    }
    
    public function getReason() {return $this->name;}
    
}

?>
