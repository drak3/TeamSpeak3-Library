<?php

declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query;
/**
 * 
 *
 * @author drak3
 */
class CommandResponse extends Response
{
    
    protected $command;
    protected $errorID;
    protected $errorMessage;
    protected $extraMessage;
    
    /**
     *
     * @param Command $c
     * @param array $items
     * @param int $errorID
     * @param string $errorMessage 
     */
    public function __construct(Command $c, array $items, $errorID=0, $errorMessage="ok", $extraMessage="" ) {
        $this->command = $c;
        $this->items = $items;
        $this->errorID = $errorID;
        $this->errorMessage = $errorMessage;
    }
    
    public function getCommand() { return $this->command;}
    public function getErrorID() { return $this->errorID;}
    public function getErrorMessage() { return $this->errorMessage;}
    public function getExtraMessage() { return $this->extraMessage;}
    
    
}

?>
