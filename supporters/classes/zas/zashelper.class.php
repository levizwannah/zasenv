<?php
   /**
    * The ZAS commandline helper for api development.
    */
    
    namespace Zas;
    
    #uns#

    class ZasHelper{

        /**
         * @var object $zasConfig Contains the configuration in the zas-config.json
         */
        private $zasConfig;
        private $rootDir;
        public static $configPath = __DIR__. "/../../../zas-config.json";


        # traits here
        use NsUtilTrait;
        #ut#

        /**
         * Loads the configuration of Zas
         */
        public function __construct()   
        {
            # Use the Zas configuration to set the extensions and path
            $this->loadConfig();
            $root = $this->zasConfig->directories->root;

            $parentDir = preg_split("/$root/", __DIR__);
            $this->rootDir = $parentDir[0].DIRECTORY_SEPARATOR."$root";
        }

        /**
         * Loads the zas configuration from the zas-config.json
         */
        private function loadConfig(){
            $config = file_get_contents(ZasHelper::$configPath);
            $this->zasConfig = json_decode($config);
        }

        

        /**
         * Prints the commands for ZAS
         * @return void
         */
        public function printHelp(){
            echo file_get_contents("cmd.txt");
        }

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
                default:
                {
                    ZasHelper::log("Didn't call any case");
                }
            }
            
        }
        
        #----------------------------------------------------
        # Functions for executing different commands
        #----------------------------------------------------

        /**
         * Executes the make command
         * @param int $argc
         * @param array $argv
         * 
         * @return void
         */
        private function execMake(int $argc, array $argv){
            $maker = new Maker($this->zasConfig);

            $container = strtolower($argv[2] ?? "");
            $containerName = $argv[3] ?? "";
            $force = false;
            $forceIndex = array_search(ZasConstants::DASH_DASH_F, $argv);
            if($forceIndex !== false) {
                $force = true;
                unset($argv[$forceIndex]);
                $argv = array_values($argv);
                $argc = count($argv);
            }
            
            switch($container){
                case ZasConstants::ZC_CLASS:
                    {
                        $interfaces = $traits = [];
                        $parentClass = "";

                        $isParent = $isTrait = $isInterface = false;
                        $states = [&$isParent, &$isTrait, &$isInterface];
                        
                        $setState = function(array &$states, int $index){
                            foreach($states as  $i => &$state){
                                   $state = false;
                                   if($i == $index) $state = true; 
                            }
                        };

                        for($i = 4; $i < $argc; $i++){
                            
                            $currentVal = $argv[$i];

                            # check for -i, -p or -t
                            switch($currentVal){
                                case ZasConstants::DASH_P:
                                    {
                                        $setState($states, 0);
                                        continue 2;
                                    }
                                case ZasConstants::DASH_T:
                                    {
                                        $setState($states, 1);
                                        continue 2;
                                    }
                                case ZasConstants::DASH_I:
                                    {
                                        $setState($states, 2);
                                        continue 2;
                                    }
                            }

                            # set the parent class
                            switch(true){
                                case $isParent:
                                    {
                                        $parent = (object)$maker->makeClass($currentVal);
                                        $parentClass = $parent->actualName;
                                        break;
                                    }
                                case $isTrait:
                                    {
                                        $trait = (object)$maker->makeTrait($currentVal);
                                        $traits[] = $trait->actualName;

                                        break;
                                    }
                                case $isInterface:
                                    {
                                        $interface = (object) $maker->makeInterface($currentVal);
                                        $interfaces[] = $interface->actualName;
                                        break;
                                    }
                            }
                        }

                        ZasHelper::log(
                            ((object)$maker->makeClass($containerName, $parentClass,$interfaces, $traits, $force))->actualName
                        );

                        break;
                    }
                case ZasConstants::ZC_INFC:
                    {
                        $interfaces = [];
                        $isIntList = false;
                        for($i = 4; $i < $argc; $i++){
                            
                            $currentVal = $argv[$i];

                            switch($currentVal){
                                case ZasConstants::DASH_E:
                                    {
                                        $isIntList = true;
                                        continue 2;
                                    }
                            }

                            switch(true){
                                case $isIntList:
                                    {
                                        # make every interface seen
                                        $interface = (object)$maker->makeInterface($currentVal);
                                        $interfaces[] = $interface->actualName;
                                        break;
                                    }
                            }
                        }

                        ZasHelper::log(
                            ((object)$maker->makeInterface($containerName, $interfaces, $force))->actualName
                        );

                        break;
                    }
                case ZasConstants::ZC_TRAIT:
                    {
                        $traits = [];
                        $isTraitList = false;
                        for($i = 4; $i < $argc; $i++){
                            
                            $currentVal = $argv[$i];

                            switch($currentVal){
                                case ZasConstants::DASH_T:
                                    {
                                        $isTraitList = true;
                                        continue 2;
                                    }
                            }

                            switch(true){
                                case $isTraitList:
                                    {
                                        # make every trait found
                                        $trait = (object)$maker->makeTrait($currentVal);
                                        $traits[] = $trait->actualName;
                                        break;
                                    }
                            }
                        }

                        ZasHelper::log(
                            ((object)$maker->makeTrait($containerName, $traits, $force))->actualName
                        );

                        break;
                    }
                case ZasConstants::ZC_CONST:
                    {
                        $parentClass = "";
                        $isParent = false;
                        for($i = 4; $i < $argc; $i++){
                            
                            $currentVal = $argv[$i];

                            switch($currentVal){
                                case ZasConstants::DASH_P:
                                    {
                                        $isParent = true;
                                        continue 2;
                                    }
                            }

                            if($isParent){
                                $parent = (object)$maker->makeConstClass($currentVal);
                                $parentClass = $parent->actualName;
                            }
                        }

                        ZasHelper::log(
                            ((object)$maker->makeConstClass($containerName, $parentClass, $force))->actualName
                        );

                        break;
                    }
                case ZasConstants::ZC_ABCLASS:
                    {
                        break;
                    }
                default:
                   {
                       ZasHelper::log("Command incomplete:: please select the container");
                       $this->printHelp();
                   }
                
            }
        }

        /**
         * Write text to the console
         * @param string $txt
         * 
         * @return void
         */
        public static function log(string $txt){
            echo "$txt\n";
        }
    }

?>