<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class Cli extends ZasHelper  {

        # use traits
        #ut#

        /**
         * Handles the commands
         * @param int $argc
         * @param array $argv
         * 
         * @return void
         */
        public function process(int &$argc, array &$argv){
            
            if($argc < 2){
                $this->printHelp();
                return;
            }

            # show list
            $mainCommand = strtolower($argv[1]);

            switch($mainCommand){
                case ZasConstants::ZC_MAKE:
                    {
                        $this->execMake($argc, $argv);
                        break;
                    }
                case ZasConstants::ZC_UPD_ROOT:
                    {
                        $this->updateRootPath();
                        break;
                    }
                case ZasConstants::ZC_RUN:
                    {
                        $this->run($argc, $argv);
                        break;
                    }
                default:
                {
                    ZasHelper::log("Didn't call any case");
                }
            }
            
        }
    }

?>