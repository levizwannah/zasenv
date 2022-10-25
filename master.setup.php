<?php
/*
|----------------------------------------------------------
| Do all necessary autoloading here.
| Vendor autoloading is done by the autoloader.
|----------------------------------------------------------
*/
require(__DIR__ . "/autoloader.class.php");

/*--------------------------------------------------------
|   GLOBAL VARIABLES
|---------------------------------------------------------
*/
$zasConfig = json_decode(file_get_contents(__DIR__ . "/zas-config.json"));

$autoloader = new Autoloader();
$autoloader->autoLoad();
    
?>