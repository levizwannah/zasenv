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
         * Removes the regex from a name.
         * for example, if the user supply xTrait when creating a trait, Trait will be removed and X will be left.
         * @param string $name
         * @param mixed $regex
         * @param int $regexPosition
         * 
         * @return string
         */
        private function cleanName(string $name, $regex, int $regexPosition){
            # update the name
            $regex = preg_replace("/\\\w{1}/", "", $regex);
            $regex = preg_replace("/\W/", "", $regex);

            #remove trait from the traitName incase it is there.
            $flUpper = strtoupper($regex[0]);
            $flLower = strtolower($regex[0]);
            $remainingLetters = substr($regex, 1);
        
            switch($regexPosition){
                case ZasConstants::R_START:
                    {
                        return preg_replace("/^[$flUpper$flLower]$remainingLetters/", "", $name);
                    }
                case ZasConstants::R_END:
                    {
                        return preg_replace("/[$flUpper$flLower]$remainingLetters$/", "", $name);
                    }
                case ZasConstants::R_ANYWHERE:
                    {
                        return preg_replace("/[$flUpper$flLower]$remainingLetters/", "", $name);
                    }
            }
            
        }

        /**
         * Makes a file regardless of its directory
         * @param string $qualifiedName - full name of the container include its namespace
         * @param string $zasPathStr - path as in Zas config. use one of `ZasConstants::ZCFG_*`
         * @param string $zasExtStr - extension as in Zas config. use one of `ZasConstants::ZCFG_*`
         * 
         * @return array `[exists => bool, actualName=>"acutalName", namespace => "namespace", homeDir => "homeDir", filePath => "filePath"]`
         */
        private function makeFile(string $qualifiedName, string $zasPathStr, string $zasExtStr){
            $namespace = $this->getNamespaceText($this->homeDir($qualifiedName));

            $homeDir = strtolower($namespace);

            # get the class name
            $actualName = $this->capitalizeWords($this->getName($qualifiedName));


            $homeDir = $this->getFullPath($this->zasConfig->path->$zasPathStr) . DIRECTORY_SEPARATOR. $homeDir;
            $core = new System();
            $core->makeDirectory($homeDir);
            
            $filePath = (object)$core->createFile($homeDir.DIRECTORY_SEPARATOR. strtolower($actualName). ".". $this->zasConfig->extensions->$zasExtStr);
            if(!$filePath->status) ZasHelper::log("System::Error::Could not create file");

            return [
                "exists" => $filePath->exists,
                "actualName" => $actualName,
                "namespace" => $namespace,
                "homeDir" => $homeDir,
                "filePath" => $filePath->fullPath
            ];
        }

        /**
         * Creates a class following the ZAS and the conventions specified in the zas-config file
         * @param string $className - qualified class name
         * @param string $parentClassName - qualified parent class name
         * @param array $impInterfaces - qualified interfaces names
         * @param array $useTraits - qualified traits names
         * 
         * @return array [actualName => "actualName", filePath => "filePath"]
         */
        public function makeClass(string $className, string $parentClassName = "", array $impInterfaces = [], array $useTraits = [], bool $force = false){

            $madeFile = (object)$this->makeFile($className, ZasConstants::ZCFG_CLASS, ZasConstants::ZCFG_CLASS);
            $namespace = $madeFile->namespace;
            $homeDir = $madeFile->homeDir;
            $actualName = $madeFile->actualName;
            $filePath = $madeFile->filePath;

            ClassObject::$temPath = $this->getFullPath($this->zasConfig->templatePath->class);
            //echo "Template path: ", ClassObject::$temPath, "\n";

            $classObj = new ClassObject([
                ClassObject::CN => $actualName,
                ClassObject::NS => preg_replace("/^\W/", "", $namespace)
            ]);

            # set properties
            $classObj->setQualifiedName($namespace ."\\".$actualName);
            $classObj->setParent($parentClassName);
            $classObj->setTraits($useTraits);
            $classObj->setInterfaces($impInterfaces);

            # check if file exist and if we want to overwrite it.
            if($madeFile->exists)   ZasHelper::log("Interface already exists. Use --f in your command to overwrite it.");

            if(($force && $madeFile->exists) || !$madeFile->exists){
                file_put_contents($filePath, $classObj->makePhpCode());
            }


            return [
                "actualName" => $classObj->getQualifiedName(),
                "filePath" => $filePath
            ];
        }

        /**
         * @param mixed $interfaceName
         * @param array $extendsInterfaces
         * @param bool $force - should we overwrite the file if it exists?
         * 
         * @return array [actualName => "actualName", filePath => "filePath"]
         */
        private function makeInterface(string $interfaceName, array $extendsInterfaces = [], bool $force = false){

            # update the name
            $regex = preg_replace("/\\\w{1}/", "", $this->zasConfig->nameConventionsRegex->interface);
            $regex = preg_replace("/\W/", "", $regex);

            # remove trait from the traitName incase it is there.
            $interfaceName = $this->cleanName($interfaceName, $regex, ZasConstants::R_END);

            $madeFile = (object)$this->makeFile($interfaceName, ZasConstants::ZCFG_IFC, ZasConstants::ZCFG_IFC);
            $namespace = $madeFile->namespace;
            $homeDir = $madeFile->homeDir;
            $actualName = $madeFile->actualName;
            $filePath = $madeFile->filePath;

            IfcObject::$temPath = $this->getFullPath($this->zasConfig->templatePath->interface);
            
            # update the name
            $actualName .= $regex;

            $ifcObj = new IfcObject([
                IfcObject::IN => $actualName,
                IfcObject::NS => preg_replace("/^\W/", "", $namespace)
            ]);

            # set properties
            $ifcObj->setQualifiedName($namespace ."\\".$actualName);
            $ifcObj->setInterfaces($extendsInterfaces);

            # check if file exist and if we want to overwrite it.
            if($madeFile->exists)   ZasHelper::log("Interface already exists. Use --f in your command to overwrite it.");

            if(($force && $madeFile->exists) || !$madeFile->exists){
                file_put_contents($filePath, $ifcObj->makePhpCode());
            }

            
            return [
                "actualName" => $ifcObj->getQualifiedName(),
                "filePath" => $filePath
            ];
                
        }

        /**
         * @param string $traitName
         * @param array $useTraits
         * @param bool $force - should we overwrite the file if it exists?
         * 
         * @return array [actualName => "actualName", filePath => "filePath"]
         */
        public function makeTrait(string $traitName, array $useTraits = [], bool $force = false){
            # update the name
            $regex = preg_replace("/\\\w{1}/", "", $this->zasConfig->nameConventionsRegex->trait);
            $regex = preg_replace("/\W/", "", $regex);

            # remove trait from the traitName incase it is there.
            $traitName = $this->cleanName($traitName, $regex, ZasConstants::R_END);
            
            $madeFile = (object)$this->makeFile($traitName, ZasConstants::ZCFG_TRAIT, ZasConstants::ZCFG_TRAIT);
            $namespace = $madeFile->namespace;
            $homeDir = $madeFile->homeDir;
            $actualName = $madeFile->actualName;
            $filePath = $madeFile->filePath;

            TraitObject::$temPath = $this->getFullPath($this->zasConfig->templatePath->trait);
            
            #append trait to the name
            $actualName .= $regex;

            $traitObj = new TraitObject([
                TraitObject::TN => $actualName,
                TraitObject::NS => preg_replace("/^\W/", "", $namespace)
            ]);

            # set properties
            $traitObj->setQualifiedName($namespace ."\\".$actualName);
            $traitObj->setTraits($useTraits);

            # check if file exist and if we want to overwrite it.
            if($madeFile->exists)   ZasHelper::log("Trait already exists. Use --f in your command to overwrite it.");

            if(($force && $madeFile->exists) || !$madeFile->exists){
                file_put_contents($filePath, $traitObj->makePhpCode());
            }

            
            return [
                "actualName" => $traitObj->getQualifiedName(),
                "filePath" => $filePath
            ];
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
                                        $parent = (object)$this->makeClass($currentVal);
                                        $parentClass = $parent->actualName;
                                        break;
                                    }
                                case $isTrait:
                                    {
                                        $trait = (object)$this->makeTrait($currentVal);
                                        $traits[] = $trait->actualName;

                                        break;
                                    }
                                case $isInterface:
                                    {
                                        $interface = (object) $this->makeInterface($currentVal);
                                        $interfaces[] = $interface->actualName;
                                        break;
                                    }
                            }
                        }

                        ZasHelper::log(
                            ((object)$this->makeClass($containerName, $parentClass,$interfaces, $traits, $force))->actualName
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
                                        $interface = (object)$this->makeInterface($currentVal);
                                        $interfaces[] = $interface->actualName;
                                        break;
                                    }
                            }
                        }

                        ZasHelper::log(
                            ((object)$this->makeInterface($containerName, $interfaces, $force))->actualName
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
                                        $trait = (object)$this->makeTrait($currentVal);
                                        $traits[] = $trait->actualName;
                                        break;
                                    }
                            }
                        }

                        ZasHelper::log(
                            ((object)$this->makeTrait($containerName, $traits, $force))->actualName
                        );

                        break;
                    }
                case ZasConstants::ZC_CONST:
                    {
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