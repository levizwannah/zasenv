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
         * Creates a directory in a subdirectory
         */
        private function makeDirectory($path){
            return (new System())->makeDirectory($path);
        }

        /**
         * Create classes following the convention specified in the zas configuration file.
         */
        public function makeClass(string $className, string $parentClassName = null, array $impInterfaces = [], array $useTraits = [],bool $constantsClass = false ){
            $namespace = $this->getNamespaceText($this->homeDir($className));

            $homeDir = strtolower($namespace);

            # get the class name
            $actualName = $this->capitalizeWords($this->getName($className));


            $homeDir = $this->rootDir . $this->zasConfig->path->class. DIRECTORY_SEPARATOR. $homeDir;
            $core = new System();
            $core->makeDirectory($homeDir);
            
            $fileName = $core->createFile($homeDir.DIRECTORY_SEPARATOR. strtolower($actualName). ".". $this->zasConfig->extensions->class);
            


        }
    }

?>