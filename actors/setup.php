<?php
/**
 * References the master setup
 * You can add custom setup procedures for actors here.
 */
(
    isset($loaded)
    &&
    isset($loaded[dirname(__DIR__)])
)
or
require(dirname(__DIR__). "/master.setup.php");
$loaded[__DIR__] = 1;
?>