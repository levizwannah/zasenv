<?php
   /**
    * The ZAS commandline helper for api development.
    */
    
    namespace Zas;
    
    class ZasHelper{
        
        /**
         * @var object $zasConfig Contains the configuration in the zas-config.json
         */
        private $zasConfig;
        private $rootDir;
        public static $configPath = __DIR__. "/../../../zas-config.json";
        /**
         * Loads the configuration of Zas
         */
        public function __construct()
        {
            # Use the Zas configuration to set the extensions and path
            $this->loadConfig();
            $root = $this->zasConfig->directories->root;

            $parentDir = preg_split("/$root/", __DIR__);
            $this->rootDir = $parentDir[0]."/$root";
        }

        /**
         * Loads the zas configuration from the zas-config.json
         */
        private function loadConfig(){
            $config = file_get_contents(ZasHelper::$configPath);
            $this->zasConfig = json_decode($config);
        }

        /**
         * Splits a name into parts by non-alpha numeric characters.
         */
        private function getParts($name){
            return preg_split("/\W/", $name);
        }

        /**
         * Creates a directory in a subdirectory
         */
        private function makeDirectory($parentPath, $subdir){
            $core = new System();
            $subdir = preg_replace("/^\d+/", "", $subdir);
            $parentPath = preg_replace("/^[\d+(\/\d+)]/", "", $parentPath);
            $fullPath = "$parentPath/$subdir";
            return $core->makeDirectory($fullPath);
        }

        /**
         * Create classes following the convention specified in the zas configuration file.
         */
        public function makeClass(string $className, string $parentClassName = null, array $impInterfaces = [], array $useTraits = [],bool $constantsClass = false ){
            $nameParts = $this->getParts($className);
            $size = count($nameParts);

            if($size < 1) return ZasConstants::ERR_CNE;

            for($i = 0; $i < $size - 1; $i++ ){
                # make the subdirectories
                $parentDir = $this->rootDir . "/" . $this->zasConfig->path->class;
                $this->makeDirectory($parentDir, $nameParts[$i]);
            }

        }
    }

?>