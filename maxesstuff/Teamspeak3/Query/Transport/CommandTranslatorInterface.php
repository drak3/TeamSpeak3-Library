<?php

declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query\Transport;

use maxesstuff\Teamspeak3\Query\Command;

/**
 *
 * @author drak3
 */
interface CommandTranslatorInterface
{
    /**
     * Translates a command to its query-representation
     * @param \maxesstuff\Teamspeak3\Query\Command $cmd
     * @return mixed the query representation
     */
    public function translate(Command $cmd);
    
    /**
     * Tests if a command could be translated to a query-understandable representation
     * @param \maxesstuff\Teamspeak3\Query\Command $cmd
     */
    public function isValid(Command $cmd); 
}

?>
