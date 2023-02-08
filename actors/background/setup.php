<?php
/**
 * Requires parent setup file
 * Add custom setup procedures for background here.
 * Add logic in here or elsewhere to restrict call to cli only and not http
 */

 (
    isset($loaded)
    &&
    isset($loaded[dirname(__DIR__)])
)
or
require(dirname(__DIR__)."/setup.php");
$loaded[__DIR__] = 1;
?>