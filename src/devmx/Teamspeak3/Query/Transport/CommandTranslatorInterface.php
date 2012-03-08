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
namespace devmx\Teamspeak3\Query\Transport;
use devmx\Teamspeak3\Query\Command;

/**
 * The base interface for all CommandTranslators
 * A commandtranslator is responsible for everything that goes from client to the Query
 * It mainly translates commands so the query understands them, therefore it also has to check the commands validity
 * @author drak3
 */
interface CommandTranslatorInterface
{

    /**
     * Translates a command to its query-representation
     * @param Command $cmd
     * @return mixed the query representation
     * @throws \devmx\Teamspeak3\Query\Exception\InvalidCommandExceptio
     */
    public function translate(Command $cmd);

    /**
     * Tests if a command could be translated to a query-understandable representation
     * @param Command $cmd
     * @return boolean if the command is valid or not
     */
    public function isValid(Command $cmd);
}

?>
