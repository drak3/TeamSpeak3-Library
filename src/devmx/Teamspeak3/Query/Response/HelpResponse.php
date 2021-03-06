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
namespace devmx\Teamspeak3\Query\Response;
use devmx\Teamspeak3\Query\Command;

/**
 *
 * @author drak3
 */
class HelpResponse extends CommandResponse
{
    /**
     * @var string 
     */
    protected $helpText = '';
    
    /**
     * Constructor.
     * @param Command $cmd
     * @param string $helpText
     * @param int $errorID
     * @param string $errorMessage
     * @param array $errorItems 
     */
    public function __construct(Command $cmd, $helpText, $errorID=0, $errorMessage='ok', $errorItems=array()) {
        parent::__construct($cmd, array(), $errorID, $errorMessage, $errorItems);
        $this->helpText = $helpText;
    }
    
    /**
     * Returns the help text
     * @return string 
     */
    public function getHelpText() {
        return $this->helpText;
    }
}

?>
