<?php
error_reporting(-1);
require_once 'bootstrap.php';
use devmx\Teamspeak3\Query;


$squery = new Query\ServerQuery(Query\QueryTransport::getCommon( "devmx.de", 10011 ));
//$squery->login("serveradmin", "j2NEmy5T");

$server = new Query\Node\Server( $squery );


$vServer = $server->login('serveradmin', 'j2NEmy5T')->getVirtualServerByPort( 9987 );

$vServer->createQueryOnServer();
echo "created channel\n";

$squery->deselect();
$vServer->createChannel( 'foo' );
echo "quit\n";

?>
