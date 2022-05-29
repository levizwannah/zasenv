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
            parent::process($argc, $argv);
        }
    }

?>