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
namespace devmx\Teamspeak3\Query\Transport\Common;
use devmx\Teamspeak3\Query\Command;
use devmx\Teamspeak3\Query\Exception\InvalidCommandException;

/**
 * The commandtranslator translates a Command object to its Query representation
 * @author drak3
 */
class CommandTranslator implements \devmx\Teamspeak3\Query\Transport\CommandTranslatorInterface
{
    /**
     * The string that seperates the commandname from the rest of the command 
     */
    const COMMANDNAME_SEPERATOR = ' ';
    
    /**
     * The option prefix 
     */
    const OPTION_PREFIX = '-';
    
    /**
     * The string that seperates an option from another option 
     */
    const OPTION_SEPERATOR = ' ';
    
    /**
     * The string that seperates two sections of parameters 
     */
    const SECTION_SEPERATOR = '|';
    
    /**
     * The string that seperates a key value pair of a parameter 
     */
    const KEY_VALUE_SEPERATOR = '=';
    
    /**
     * The string that seperates a parameter from another 
     */
    const PARAMETER_SEPERATOR = ' ';
    
    /**
     * The string that signals the end of the command
     */
    const COMMAND_DELIMITER = "\n";
    

    /**
     * Translates a command to its query-representation
     * e.g Command('channelcreate', array('name'=>'new channel'), array('force')) becomes "channelcreate name=>new\schannel -force<newline>
     * the command parameters may include key=>value pairs and arrays with key=>value pairs, arrays are translated to sections
     * e.g Command('clientkick', array( array('cid'=>1, 'reason' => 'haha'), array('cid'=>2, 'reason'=>'oops!')) becomes clientkick cid=1 reason=haha|cid=2 reason=oops!<newline>
     * @param \devmx\Teamspeak3\Query\Command $cmd
     * @throws \devmx\Teamspeak3\Query\InvalidCommandException if the command is not valid
     * @return string the query representation 
     */
    public function translate(Command $cmd)
    {  
        $this->checkCommand($cmd);

        $queryRepresentation  = $this->translateName($cmd->getName());
        $params = $this->translateParameters($cmd->getParameters());
        if($params !== '') {
            $queryRepresentation .= $params . self::PARAMETER_SEPERATOR;
        }
        $queryRepresentation .= $this->translateOptions($cmd->getOptions());

        
        $queryRepresentation = \rtrim($queryRepresentation);
        $queryRepresentation .= self::COMMAND_DELIMITER;
        return $queryRepresentation;
    }
    
    
    /**
     * Checks if the given command is valid
     * @param \devmx\Teamspea3\Query\Command $cmd
     * @return boolean
     */
    public function isValid(\devmx\Teamspeak3\Query\Command $cmd)
    {
        try {
            $this->checkCommand($cmd);
        } catch(InvalidCommandException $e) {
            return false;
        }
        return true;
    }
    
    /**
     * Translates a name to its queryrepresentation
     * @param string $name
     * @return string 
     */
    protected function translateName($name) {
        return $this->escape($name).self::COMMANDNAME_SEPERATOR;
    }
    
    /**
     * Translates an array io parameters
     * @param array $params
     * @return string 
     */
    protected function translateParameters(array $params) {
        $queryRepresentation = '';
        foreach($params as $name => $value) {
            if(is_array($value)) {
                //thanks to isValid we can rely on the fact that value does not contain other arrays
                $queryRepresentation .= $this->translateParameters($value) . self::SECTION_SEPERATOR;
            }
            else {
                $queryRepresentation .= $this->escape($name) . self::KEY_VALUE_SEPERATOR . $this->escape($value) . self::PARAMETER_SEPERATOR;
            }
        }
        return rtrim($queryRepresentation, self::SECTION_SEPERATOR . self::PARAMETER_SEPERATOR);
    }
    
    /**
     * Translates an array of options
     * @param array $options
     * @return string 
     */
    protected function translateOptions(array $options) {
        $queryRepresentation = '';
        foreach ($options as $name)
        {
            $queryRepresentation .= self::OPTION_PREFIX . $this->escape($name) . self::OPTION_SEPERATOR;
        }
        return $queryRepresentation;
    }

    /**
     * Escapes a value so it can be used on the commonQuery
     * True is translated to 1, false to 0
     * @param string|bool $value
     * @return string
     */
    protected function escape($value)
    {
        if(is_bool($value)) {
            if($value === TRUE) {
                return '1';
            }
            else {
                return '0';
            }
        }
        else {
            $to_escape = Array("\\", "/", "\n", " ", "|", "\a", "\b", "\f", "\n", "\r", "\t", "\v");
            $replace_with = Array("\\\\", "\/", "\\n", "\\s", "\\p", "\\a", "\\b", "\\f", "\\n", "\\r", "\\t", "\\v");
            return str_replace($to_escape, $replace_with, $value);
        } 
    }    
    
    /**
     * Checks a command for validity and throws an Exception including detailed information when the command is not valid
     * @throws \devmx\Teamspeak3\Query\Exception\InvalidCommandException 
     */
    protected function checkCommand(Command $command) {
        $this->checkName($command->getName(), $command);
        $this->checkParameters($command->getParameters(), $command);
        $this->checkOptions($command->getOptions(), $command);
    }   

    /**
     * Checks if the Name is valid to send it to a CommonQuery
     * @param string $name
     * @throws \devmx\Teamspeak3\Query\Exception\InvalidCommandException 
     */
    protected function checkName($name, Command $cmd)
    {
        if (!$this->isValidName($name))
        {
            throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_NAME, $name);
        }
    }
    
    /**
     * Validates a name
     * @param string $name
     * @return boolean true if valid, false if not 
     */
    protected function isValidName($name) {
        if (!is_string($name) || preg_match("/^[0-9a-z_-]*$/iD", $name) == 0)
        {
            return false;
        }
        return true;
    }

    /**
     * Checks if an array of parameters is valid
     * Parameters may contain key=>value pairs, where the value must not be an array and must be an boolean or a type that can be converted to a string
     * Parameters may contain arrays which contain key=>value pairs which must follow the rules above
     * @param array $params
     * @throws \devmx\Teamspeak3\Query\Exception\InvalidCommandException 
     */
    protected function checkParameters(array $params, Command $cmd)
    {
        foreach($params as $name => $value) {
            if(is_array($value)) {
                foreach($value as $name2 => $value2) {
                    if(!$this->isValidName($name2)) {
                        throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_PARAMETER_NAME, $name2);
                    }
                    if(!$this->isValidValue($value2)) {
                        throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_PARAMETER_VALUE, $value2);
                    }
                }
            }
            else {
                if(!$this->isValidName($name)) {
                    throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_PARAMETER_NAME, $name);
                }
                if(!$this->isValidValue($value)) {
                    throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_PARAMETER_VALUE, $value);
                }
            }
        }
    }
    
    /**
     * Validates a value
     * @param mixed $value
     * @return boolean 
     */
    protected function isValidValue($value) {
        if(is_array($value)) {
            return false;
        }
        if(is_string($value) || is_bool( $value ) || is_int( $value) || is_float( $value )) {
            return true;
        }
        try {
            $value = (string) $value;
            return true;
        } catch(\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * Returns if the optionslist is valid or not
     * Optionnames have to be valid names and must not include other chars than a-z,- and 0-9
     * an option must not start with a '-'
     * @param array $options
     * @return bool
     */
    protected function checkOptions(array $options, Command $cmd)
    {
        foreach ($options as $name)
        {
            if (!$this->isValidName($name) || $this->hasOptionPrefix($name))
            {
                throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_OPTION, $name);
            }
        }
        return true;
    }
    
    /**
     * Checks if the given string is prefixed with the option prefix
     * @param string $name
     * @return boolean 
     */
    private function hasOptionPrefix($name) {
        if(isset($name[0])) {
            if($name[0] === self::OPTION_PREFIX) {
                return true;
            }
        }
        return false;
    }

}

?>
