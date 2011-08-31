<?php
declare(encoding="UTF-8");
namespace maxesstuff\Teamspeak3\Query\Transport;

/**
 *
 * @author drak3
 */
interface ResponseHandlerInterface
{
    public function getResponseInstance( \maxesstuff\Teamspeak3\Query\Command $cmd, $raw);

    public function isCompleteResponse($raw);

    public function isCompleteEvent($raw);

    public function getWelcomeMessageLength();

    public function isWelcomeMessage($welcome);

    public function getEventInstances($raw);
}

?>
