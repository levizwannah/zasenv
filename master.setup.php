<?php
    /*
    | Do all necessary autoloading here.
    | Vendor autoloading should be done here also.
    */
    require(__DIR__ . "/autoloader.class.php");

    $autoloader = new Autoloader();
    $autoloader->autoLoad();
    
?>