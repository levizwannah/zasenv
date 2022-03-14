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
         * Splits a name into parts by non-alpha numeric characters.
         */
        public function removeSlashes($name){
            return preg_replace("/(^\W+)|(\W+$)/", "", $name);
        }

        /**
         * Given a fully qualified name, return the directory path component of the name.
         */
        public function homeDir($qualifiedName){
            return preg_replace("/(\W+)?[\w]+$/", "", $qualifiedName);
        } 

        /**
         * Creates a directory in a subdirectory
         */
        private function makeDirectory($path){
            return (new System())->makeDirectory($path);
        }

        /**
         * Returns the name from the qualified name.
         * for example, levi\zwannah will return zwannah.
         */
        public function getName($qualifiedName){
            $actualName = preg_split("/\W/", $qualifiedName);
            end($actualName);
            return current($actualName);
        }

        /**
         * Capitalizes letters in a string 
         * using $separator = " \t\r\n\f\v'/-.|\\"
         */
        public function capitalizeWords($string){
            return ucwords($string, " \t\r\n\f\v'/-.|\\");
        }

        /**
         * Makes a directory path a valid php namesapce name.
         */
        public function getNamespaceText($name){
            $name = preg_replace("/\W/", "\\", $name);
            if($name[0] !== "\\") $name = "\\".$name;
            
            return $this->capitalizeWords($name);
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