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
            $found = parent::process($argc, $argv);

            if($found) return true;
            # add your commands here in a switch statement
            # use the ZasConstants to define your constants
            # show list
            $mainCommand = strtolower($argv[1]);

            Cli::log("You enter an unknown command");
            return false;
        }
    }

?>