<?php
error_reporting(-1);
require_once 'bootstrap.php';
use devmx\Teamspeak3\Query;


$squery = new Query\ServerQuery(Query\QueryTransport::getCommon( "devmx.de", 10011 ));
$squery->connect();
$squery->login("serveradmin", "j2NEmy5T");

$server = new Query\Node\Server( $squery );

$server->getVirtualServerByPort( 9987 )->createChannel( 'foo' );
$squery->refreshWhoAmI();

echo "created channel\n";

$squery->quit();
echo "quit\n";

?>
