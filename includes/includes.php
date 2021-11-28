<?php
function myLoader($class){
    $dir = __DIR__ . "/classes/";
    require_once ($dir.strtolower($class). ".php");
}
spl_autoload_register('myLoader');
require_once ("config.php");
require_once ("utility.php");