<?php
/**
 * Requires parent setup file
 * Add custom set up procedures for foreground here.
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