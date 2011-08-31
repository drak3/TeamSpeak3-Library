<?php

declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query;
/**
 * A response caused directly by a command (e.g. channellist)
 * @author drak3
 */
class CommandResponse extends Response
{
    /**
     *
     * @var \maxesstuff\Teamspeak3\Query\Command 
     */
    protected $command;
    /**
     *
     * @var string 
     */
    protected $errorID;
    /**
     *
     * @var string 
     */
    protected $errorMessage;
    /**
     *
     * @var string
     */
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
    
    /**
     * Returns the command that caused the response
     * @return \maxesstuff\Teamspeak3\Query\Command 
     */
    public function getCommand() { return $this->command;}
    /**
     * Returns the error code of the response
     * @return int 
     */
    public function getErrorID() { return $this->errorID;}
    /**
     * Returns the error message of the response
     * @return string 
     */
    public function getErrorMessage() { return $this->errorMessage;}
    /**
     * Returns the extra message of the response (empty string if none as 
     * @return string 
     */
    public function getExtraMessage() { return $this->extraMessage;}
    
    
}

?>
