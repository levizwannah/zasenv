<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


/**
 * Maker is the object that does the heavy lifting behind every `zas make` command.
 * It makes the classes, interfaces, etc.
 */
    class Maker {


        /**
         * The root directory as specified in the Zas Config
         * @var string
         */
        private $rootDir = "";
        
        /**
         * The Zas config object
         * @var object
         */
        private $zasConfig;

        # use traits
        use NsUtilTrait;
        #ut#

        /**
         * Creates a new maker object.
         * @param object $zasConfig
         */
        public function __construct(object $zasConfig){
            $this->zasConfig = $zasConfig;
            $root = $this->zasConfig->directories->root;

            $parentDir = preg_split("/$root/", __DIR__);
            $this->rootDir = $parentDir[0].DIRECTORY_SEPARATOR."$root";
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
            if($madeFile->exists)   ZasHelper::log("Class already already exists. Use --f in your command to overwrite it.");

            if(($force && $madeFile->exists) || !$madeFile->exists){
                file_put_contents($filePath, $classObj->makePhpCode());
            }


            return [
                "actualName" => $classObj->getQualifiedName(),
                "filePath" => $filePath
            ];
        }

        /**
         * Creates a constatns class following the ZAS and the conventions specified in the zas-config file
         * @param string $className - qualified class name
         * @param string $parentClassName - qualified parent class name
         * 
         * @return array [actualName => "actualName", filePath => "filePath"]
         */
        public function makeConstClass(string $className, string $parentClassName = "", bool $force = false){
            # update the name
            $regex = preg_replace("/\\\w{1}/", "", $this->zasConfig->nameConventionsRegex->constantsClass);
            $regex = preg_replace("/\W/", "", $regex);

            # remove trait from the traitName incase it is there.
            $className = $this->cleanName($className, $regex, ZasConstants::R_END);


            $madeFile = (object)$this->makeFile($className, ZasConstants::ZCFG_CONST, ZasConstants::ZCFG_CONST);
            $namespace = $madeFile->namespace;
            $homeDir = $madeFile->homeDir;
            $actualName = $madeFile->actualName;
            $filePath = $madeFile->filePath;

            ClassObject::$temPath = $this->getFullPath($this->zasConfig->templatePath->constantsClass);
            
            # update the name
            $actualName .= $regex;

            $classObj = new ClassObject([
                ClassObject::CN => $actualName,
                ClassObject::NS => preg_replace("/^\W/", "", $namespace),
                ClassObject::C_VISIBILITY => "private"
            ]);

            # set properties
            $classObj->setQualifiedName($namespace ."\\".$actualName);
            $classObj->setParent($parentClassName);

            # check if file exist and if we want to overwrite it.
            if($madeFile->exists)   ZasHelper::log("Constant class already exists. Use --f in your command to overwrite it.");

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
        public function makeInterface(string $interfaceName, array $extendsInterfaces = [], bool $force = false){

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

    }

?>