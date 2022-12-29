<?php
// Comments with #.# are required by `zas` for code insertion.

namespace CustomZas;

#uns#


/**
 * Put your custom zas command implementation in this file.
 */
class Cli extends ZasHelper  {
    
    /**
     * Handles the commands
     * @param int $argc
     * @param array $argv
     * 
     * @return void
     */
    public function process(int &$argc, array &$argv){

        # checking if this command exists already
        $found = parent::process($argc, $argv);

        # if found, then exit
        if($found) return true;

        # else zas checks here. so,
        # add your commands here in a switch statement
        # use the ZasConstant to define your constants

        $mainCommand = strtolower($argv[1]); // for example 'make' is the main
                                             // command in `php zas make class`

        switch($mainCommand) {
            # code goes here
            # case ZasConstant::ZC_CREATE: then $this->doSomething($argc, $argv) 
            # doSomething($argc, $argv) are implemented
            # in supporters/classes/custom-zas/zas-helper.class.php
            # ZasConstant::ZC_CREATE: is defined in 
            # supporters/constants/custom-zas/zas.constants.php
        }

        Cli::log("You enter an unknown command");
        return false;
    }
}

?>