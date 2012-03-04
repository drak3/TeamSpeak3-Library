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
 *
 * @author drak3
 */
class InvalidCommandException extends InvalidArgumentException
{
    const INVALID_NAME = 0;
    const INVALID_PARAMETER_NAME = 1;
    const INVALID_PARAMETER_VALUE = 2;
    const INVALID_OPTION = 3;
    
    private $command;
    private $invalidityType;
    private $invalidValue;
    
    protected $messages = array(
        self::INVALID_NAME => 'name "%s"',
        self::INVALID_PARAMETER_NAME => 'parameter name "%s"',
        self::INVALID_PARAMETER_VALUE => 'parameter value "%s"',
        self::INVALID_OPTION => 'option "%s"',
    );
    
    public function __construct(Command $command, $invalidityType, $invalidValue) {
        parent::__construct($this->buildMessage($command, $invalidityType, $invalidValue));
        $this->command = $command;
        $this->invalidityType = $invalidityType;
        $this->invalidValue = $invalidValue;
    }
    
    protected function buildMessage(Command $command, $type, $value) {
        return sprintf('Invalid command "%s" because '.$this->messages[$type].'is invalid', $command->getName(), $value);
    }
    
    public function getInvalidCommand() {
        return $this->command;
    }
    
    public function getInvalidityType() {
        return $this->invalidityType;
    }
    
    public function geInvalidValue() {
        return $this->invalidValue;
    }
}

?>
