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

        private function getFullPath(string $path){
            return $this->rootDir . $path;
        }
        
        /**
         * Creates a class following the ZAS and the conventions specified in the zas-config file
         * @param string $className - qualified class name
         * @param string $parentClassName - qualified parent class name
         * @param array $impInterfaces - qualified interfaces names
         * @param array $useTraits - qualified traits names
         * 
         * @return string $fileName;
         */
        public function makeClass(string $className, string $parentClassName = "", array $impInterfaces = [], array $useTraits = []){
            $namespace = $this->getNamespaceText($this->homeDir($className));

            $homeDir = strtolower($namespace);

            # get the class name
            $actualName = $this->capitalizeWords($this->getName($className));


            $homeDir = $this->getFullPath($this->zasConfig->path->class) . DIRECTORY_SEPARATOR. $homeDir;
            $core = new System();
            $core->makeDirectory($homeDir);
            
            $fileName = $core->createFile($homeDir.DIRECTORY_SEPARATOR. strtolower($actualName). ".". $this->zasConfig->extensions->class);
            
            ClassObject::$temPath = $this->getFullPath($this->zasConfig->templatePath->class);
            //echo "Template path: ", ClassObject::$temPath, "\n";

            $classObj = new ClassObject([
                ClassObject::CN => $actualName,
                ClassObject::NS => preg_replace("/^\W/", "", $namespace)
            ]);

            # set properties
            $classObj->setQualifiedName($className);
            $classObj->setParent($parentClassName);
            $classObj->setTraits($useTraits);
            $classObj->setInterfaces($impInterfaces);

            file_put_contents($fileName, $classObj->makePhpCode());
            return $fileName;
        }

        /**
         * @param mixed $interfaceName
         * @param array $extendsInterfaces
         * 
         * @return string
         */
        private function makeInterface(string $interfaceName, array $extendsInterfaces){

            return "";
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
            $mainCommand = strtolower($argv[0]);

            switch($mainCommand){
                case ZasConstants::Z_MAKE:
                    {
                        $this->execMake($argc, $argv);
                        break;
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
         * @return [type]
         */
        private function execMake(int $argc, array $argv){
            $container = strtolower($argv[2]);
            $containerName = $argv[3];

            switch($container){
                case ZasConstants::Z_CLASS:
                    {
                        
                        break;
                    }
                case ZasConstants::Z_INFC:
                    {
                        break;
                    }
                case ZasConstants::Z_TRAIT:
                    {
                        break;
                    }
                case ZasConstants::Z_CONST:
                    {
                        break;
                    }
                case ZasConstants::Z_ABCLASS:
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