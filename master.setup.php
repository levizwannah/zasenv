<?php
/*
|----------------------------------------------------------
| Do all necessary auto-loading here.
| Vendor auto-loading is done by the auto-loader.
|----------------------------------------------------------
*/

use CustomZas\Cli;

require(__DIR__ . "/auto-loader.class.php");

/*--------------------------------------------------------
|   GLOBAL VARIABLES
|---------------------------------------------------------
*/
$zasConfig = json_decode(file_get_contents(__DIR__ . "/zas-config.json"));

/**
 * Keeps the hashmap of loaded Setups
 * @var array<string,int>
 */
$loaded = [__DIR__ => 1];

$autoLoader = new AutoLoader();
$autoLoader->autoLoad();
?>