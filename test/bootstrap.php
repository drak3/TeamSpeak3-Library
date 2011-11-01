<?php

spl_autoload_register( function($class) {
    $path = str_replace("\\","/", $class);
    $path = "/home/drak3/projects/TeamSpeak3-Framework/".$path.".php";
    if(\file_exists($path))
        require_once $path;
});


?>

