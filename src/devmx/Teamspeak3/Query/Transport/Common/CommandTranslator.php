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
 * 
 *
 * @author drak3
 */
class CommandTranslator implements \devmx\Teamspeak3\Query\Transport\CommandTranslatorInterface
{
    
    const COMMANDNAME_SEPERATOR = ' ';
    const OPTION_PREFIX = '-';
    const OPTION_SEPERATOR = ' ';
    const SECTION_SEPERATOR = '|';
    const KEY_VALUE_SEPERATOR = '=';
    const PARAMETER_SEPERATOR = ' ';
    const COMMAND_SEPERATOR = "\n";
    

    /**
     * Translates a command to its query-representation
     * @param \devmx\Teamspeak3\Query\Command $cmd
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
        $queryRepresentation .= self::COMMAND_SEPERATOR;
        return $queryRepresentation;
    }
    
    protected function translateName($name) {
        return $this->escape($name).self::COMMANDNAME_SEPERATOR;
    }
    
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
    
    protected function translateOptions(array $options) {
        $queryRepresentation = '';
        foreach ($options as $name)
        {
            $queryRepresentation .= self::OPTION_PREFIX . $this->escape($name) . self::OPTION_SEPERATOR;
        }
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
     * @throws Exception\InvalidCommandException 
     */
    protected function checkCommand(Command $command) {
        $this->checkName($command->getName(), $command);
        $this->checkParameters($command->getParameters(), $command);
        $this->checkOptions($command->getOptions(), $command);
    }

    /**
     * Escapes a value so it can be used on the commonQuery
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
     * Checks if the Name is valid to send it to a CommonQuery
     * @param string $name
     * @return boolean
     */
    protected function checkName($name, Command $cmd)
    {
        if (!$this->isValidName($name))
        {
            throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_NAME, $name);
        }
        return true;
    }
    
    protected function isValidName($name) {
        if (!is_string($name) || preg_match("/^[0-9a-z_-]*$/iD", $name) == 0)
        {
            return false;
        }
        return true;
    }

    /**
     * Returns if the Parameterlist is valid
     * Parameterlist may contain 
     * @param array $params
     * @return boolean
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
        return true;
    }
    
    
    protected function isValidValue($value) {
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
     * @param array $options
     * @return bool
     */
    protected function checkOptions(array $options, Command $cmd)
    {
        foreach ($options as $name)
        {
            if (!$this->isValidName($name))
            {
                throw new InvalidCommandException($cmd, InvalidCommandException::INVALID_OPTION, $name);
            }
        }
        return true;
    }

}

?>
