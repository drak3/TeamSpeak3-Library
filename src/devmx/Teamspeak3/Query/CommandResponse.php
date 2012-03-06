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
namespace devmx\Teamspeak3\Query;

/**
 * A response caused directly by a command (e.g. channellist)
 * @author drak3
 */
class CommandResponse extends Response
{
    
    const ERROR_ID_OK = 0;
    
    /**
     * The command which caused this response
     * @var \devmx\Teamspeak3\Query\Command 
     */
    protected $command;
    
    /**
     * The error id
     * @var int 
     */
    protected $errorID;
    
    /**
     * The error message
     * @var string 
     */
    protected $errorMessage;
    
    /**
     * The error values (like extra_message or failed_permid)
     * @var string
     */
    protected $errorValues;
    
    /**
     * Constructor
     * @param Command $c the command which caused this response
     * @param array $items the retured items
     * @param int $errorID the error id
     * @param string $errorMessage The error message
     * @param array $errorValues Additional error values
     */
    public function __construct(Command $c, array $items=array(), $errorID=0, $errorMessage="ok", $errorValues=array()) {
        $this->command = $c;
        $this->items = $items;
        $this->errorID = $errorID;
        $this->errorMessage = $errorMessage;
        $this->errorValues = $errorValues;
    }
    
    /**
     * Returns the command that caused the response
     * @return \devmx\Teamspeak3\Query\Command 
     */
    public function getCommand() { 
        return $this->command;
    }
    
    /**
     * Returns the error code of the response
     * @return int 
     */
    public function getErrorID() { 
        return $this->errorID;
    }
    
    /**
     * Returns the error message of the response
     * @return string 
     */
    public function getErrorMessage() { 
        return $this->errorMessage;
    }
    
    /**
     * Returns the extra message of the response (empty string if none as 
     * @return string 
     */
    public function getExtraMessage() { return $this->getErrorValue('extra_message');}
    
    /**
     * Returns if an error occured while executing this command
     * @return boolean
     */
    public function errorOccured() {
        return ($this->errorID !== static::ERROR_ID_OK);
    }
    
    /**
     * If an error occured, it will throw an CommandFailedException
     * @throws Exception\CommandFailedException 
     */
    public function toException() {
        if($this->errorOccured()) {
            throw new Exception\CommandFailedException($this);
        }
    }
    
    /**
     * Returns a specific error value
     * @param string $name the name of the error value
     * @param string $else if there is no such errorvalue, $else is returned
     * @return mixed
     */
    public function getErrorValue( $name, $else='')
    {
        if(isset($this->errorValues[$name])) {
            return $this->errorValues[$name];
        }
        return $else;
    }
    
    /**
     * Returns if there is a error value with given name
     * @param string $name
     * @return mixed 
     */
    public function hasErrorValue($name) {
        return isset($this->errorValues[$name]);
    }
    
    
}

?>
