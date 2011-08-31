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
    public function translate(Command $cmd);

    public function isValid(Command $cmd); 
}

?>
