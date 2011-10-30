<?php
require_once 'bootstrap.php';
use devmx\Teamspeak3\Query;


$query = Query\QueryTransport::getCommon( "79.133.47.201", 10011 );
$query->connect();
var_dump($query->sendCommand(Query\Command::simpleCommand( "use", Array("port" => 20007) )));
var_dump($query->sendCommand(new Query\Command("clientlist")));        

?>
