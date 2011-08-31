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
    /**
     *
     * @var string
     */
    protected $reason;
    
    /**
     *
     * @param string $reason the EventReason
     * @param array $items the items(data) of the event
     */
    public function __construct($reason, array $items) {
        $this->reason = $reason;
        $this->items = $items;            
    }
    
    /**
     * Returns the reason of the event
     * @return string 
     */
    public function getReason() {return $this->name;}
    
}

?>
