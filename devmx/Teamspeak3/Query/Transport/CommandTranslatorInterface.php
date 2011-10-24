<?php

declare(encoding="UTF-8");
namespace devmx\Teamspeak3\Query\Transport;

use devmx\Teamspeak3\Query\Command;

/**
 *
 * @author drak3
 */
interface CommandTranslatorInterface
{
    /**
     * Translates a command to its query-representation
     * @param \devmx\Teamspeak3\Query\Command $cmd
     * @return mixed the query representation
     */
    public function translate(Command $cmd);
    
    /**
     * Tests if a command could be translated to a query-understandable representation
     * @param \devmx\Teamspeak3\Query\Command $cmd
     */
    public function isValid(Command $cmd); 
}

?>
