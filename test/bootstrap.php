<?php

spl_autoload_register( function($class) {
    $path = str_replace("\\","/", $class);
    $path1 = __DIR__.'/../'.$path.".php";
    $path2 = __DIR__.'/'.$path.'.php';
    $path2 = str_replace("Test/", '', $path2);
    if(\file_exists($path1))
        require_once $path1;
    if(\file_exists($path2))
        require_once $path2;
});


?>

