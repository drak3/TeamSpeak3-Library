<?php
error_reporting(-1);
require_once 'bootstrap.php';
use devmx\Teamspeak3\Query;


$squery = new Query\ServerQuery(Query\QueryTransport::getCommon( "devmx.de", 10011 ));
$squery->connect();
//var_dump($query);
var_dump($squery);
var_dump($query->sendCommand(new Query\Command("clientlist")));        

?>
