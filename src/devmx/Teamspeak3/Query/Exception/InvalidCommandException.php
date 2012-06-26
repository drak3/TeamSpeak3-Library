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
namespace devmx\Teamspeak3\Query\Exception;
use devmx\Teamspeak3\Query\Command;

/**
 * This Exception is thrown when you are trying to send an invalid command, which could not be translated by the CommandTranslatorInterface
 * @author drak3
 */
class InvalidCommandException extends InvalidArgumentException
{
    /**
     * Invalid command name 
     */
    const INVALID_NAME = 0;
    
    /**
     * Invalid parameter name 
     */
    const INVALID_PARAMETER_NAME = 1;
    
    /**
     * Invalid parameter value 
     */
    const INVALID_PARAMETER_VALUE = 2;
    
    /**
     * Invalid option name 
     */
    const INVALID_OPTION = 3;
    
    /**
     * The invalid command
     * @var devmx\Teamspeak3\Query\Command
     */
    private $command;
    
    /**
     * The type of the invalidity
     * see the class constant for possible types
     * @var int
     */
    private $invalidityType;
    
    /**
     * The concrete invalid value
     * @var mixed
     */
    private $invalidValue;
    
    /**
     * The messages corresponding to the invalidity types
     * @var array of string
     */
    protected $messages = array(
        self::INVALID_NAME => 'name "%s"',
        self::INVALID_PARAMETER_NAME => 'parameter name "%s"',
        self::INVALID_PARAMETER_VALUE => 'parameter value "%s"',
        self::INVALID_OPTION => 'option "%s"',
    );
    
    /**
     * Constructor
     * @param Command $command the invalid command
     * @param int $invalidityType the invalidity type. See the class constants for possible values
     * @param mixed $invalidValue the concrete invalid value
     */
    public function __construct(Command $command, $invalidityType, $invalidValue) {
        parent::__construct($this->buildMessage($command, $invalidityType, $invalidValue));
        $this->command = $command;
        $this->invalidityType = $invalidityType;
        $this->invalidValue = $invalidValue;
    }    
    
    /**
     * Returns the invalid command
     * @return \devmx\Teamspeak3\Query\Command
     */
    public function getInvalidCommand() {
        return $this->command;
    }
    
    /**
     * Returns the invaliditytype. See the class constatns for more detailed explanaition
     * @return int
     */
    public function getInvalidityType() {
        return $this->invalidityType;
    }
    
    /**
     * Returns the concrete invalid value
     * @return mixed 
     */
    public function getInvalidValue() {
        return $this->invalidValue;
    }
    
    /**
     * Builds up the Exceptionmessage
     * It includes information about the commandname, the invaliditytype and the invalid value
     * The command-name and the invalid value are tryed converted to a readable string
     * @param Command $command
     * @param int $type
     * @param mixed $value
     * @return string 
     */
    private function buildMessage(Command $command, $type, $value) {
        $name = $this->convertToString($command->getName());
        $value = $this->convertToString($value);
        return sprintf('Invalid command "%s" because '.$this->messages[$type].' is invalid.', $name, $value);
    }
    
    /**
     * Tries to convert a value to a readable string
     * true and false are converted to "<boolean false>"/"<boolean true>"
     * objects are converted to "<object of class 'get_class($obj)'>"
     * callables are converted to "<callable>"
     * @param mixed $toString the value to convert
     * @return string 
     */
    private function convertToString($toString) {
        if($toString === true) {
            return '<boolean true>';
        }
        if($toString === false) {
            return '<boolean false>';
        }
        try {
            $converted = (string) $toString;
            return $converted;
        } catch (\Exception $e) {
            if (is_object($toString)) {
                return sprintf('<object of class "%s">', get_class($toString));
            }
        }
        if(is_callable( $toString )) {
            return '<callable>';
        }
    }
}

?>
