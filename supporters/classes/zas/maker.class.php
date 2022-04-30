<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


/**
 * Maker is the object that does the heavy lifting behind every `zas make` command.
 * It makes the classes, interfaces, etc.
 */
    class Maker extends AbstractCommandExecutor{

        
        /**
         * @param string $filePath
         * 
         * @return array $functions - function headers
         */
        public function getFuncToImplement(string $filePath){
            $fileContents = file_get_contents($filePath);
            $unwantedCnt = preg_split("/(public|protected)?\s+?function\s+\w+\(.*\)\s*;/", $fileContents, -1, PREG_SPLIT_NO_EMPTY);

            foreach($unwantedCnt as $unwanted){
                $fileContents = str_replace("$unwanted", "", $fileContents);
            }
            $fileContents = preg_replace("/\s+;/", ";", $fileContents);

            $fcArray = preg_split("/;/", $fileContents, -1, PREG_SPLIT_NO_EMPTY);
            return $fcArray;
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

            $namespace = $this->getNamespaceText($this->homeDir($name));
            $actualName = $this->capitalizeWords($this->getName($name));

            switch($regexPosition){
                case ZasConstants::R_START:
                    {
                        return "$namespace\\".preg_replace("/^[$flUpper$flLower]$remainingLetters/", "", $actualName);
                    }
                case ZasConstants::R_END:
                    {
                        return "$namespace\\".preg_replace("/[$flUpper$flLower]$remainingLetters$/", "", $actualName);
                    }
                case ZasConstants::R_ANYWHERE:
                    {
                        return "$namespace\\".preg_replace("/[$flUpper$flLower]$remainingLetters/", "", $actualName);
                    }
            }
            
            return $this;
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
            ZasHelper::log("Making class: $className");


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
            if($madeFile->exists)   ZasHelper::log("$actualName Class already already exists. Use --f in your command to overwrite it.");

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
            ZasHelper::log("Making Constants Class: $className");

            $classObj = new ClassObject([
                ClassObject::CN => $actualName,
                ClassObject::NS => preg_replace("/^\W/", "", $namespace),
                ClassObject::C_VISIBILITY => "private"
            ]);

            # set properties
            $classObj->setQualifiedName($namespace ."\\".$actualName);
            $classObj->setParent($parentClassName);
            
            # check if file exist and if we want to overwrite it.
            if($madeFile->exists)   ZasHelper::log("$actualName Constants class already exists. Use --f in your command to overwrite it.");

            if(($force && $madeFile->exists) || !$madeFile->exists){
                file_put_contents($filePath, $classObj->makePhpCode());
            }


            return [
                "actualName" => $classObj->getQualifiedName(),
                "filePath" => $filePath
            ];
        }

        /**
         * Creates an Abstract class following the ZAS and the conventions specified in the zas-config file
         * @param string $className - qualified class name
         * @param string $parentClassName - qualified parent class name
         * @param array $impInterfaces - qualified interfaces names
         * @param array $useTraits - qualified traits names
         *  
         * @return array [actualName => "actualName", filePath => "filePath"]
         */
        public function makeAbstractClass(string $className, string $parentClassName = "", array $impInterfaces = [], array $useTraits = [], bool $force = false){
            # update the name
            $regex = preg_replace("/\\\w{1}/", "", $this->zasConfig->nameConventionsRegex->abstractClass);
            $regex = preg_replace("/\W/", "", $regex);

            # remove Abstract from the className incase it is there.
            $className = $this->cleanName($className, $regex, ZasConstants::R_START);


            $madeFile = (object)$this->makeFile($className, ZasConstants::ZCFG_ACLASS, ZasConstants::ZCFG_ACLASS);
            $namespace = $madeFile->namespace;
            $homeDir = $madeFile->homeDir;
            $actualName = $madeFile->actualName;
            $filePath = $madeFile->filePath;

            ClassObject::$temPath = $this->getFullPath($this->zasConfig->templatePath->abstractClass);
            ZasHelper::log("Making Abstract Class: $className");
            # update the name
            $actualName = $regex . $actualName;

            $classObj = new ClassObject([
                ClassObject::CN => $actualName,
                ClassObject::NS => preg_replace("/^\W/", "", $namespace),
            ]);

            # set properties
            $classObj->setQualifiedName($namespace ."\\".$actualName);
            $classObj->setParent($parentClassName);
            $classObj->setTraits($useTraits);
            $classObj->setInterfaces($impInterfaces);

            # check if file exist and if we want to overwrite it.
            if($madeFile->exists)   ZasHelper::log("$actualName abstract class already exists. Use --f in your command to overwrite it.");

            if(($force && $madeFile->exists) || !$madeFile->exists){
                file_put_contents($filePath, $classObj->makePhpCode());
            }


            return [
                "actualName" => $classObj->getQualifiedName(),
                "filePath" => $filePath
            ];
        }


        /**
         * Makes a type of class based on the name of the class.
         * @param string $className
         * @param string $parentClassName
         * @param array $impInterfaces
         * @param array $useTraits
         * @param bool $force
         * 
         * @return [type]
         */
        public function makeSpecifiedClass(string $className, string $parentClassName = "", array $impInterfaces = [], array $useTraits = [], bool $force = false){
            $actualName = $this->getName($className);
            ZasHelper::log("ActualName $actualName");

            $abRegex = $this->zasConfig->nameConventionsRegex->abstractClass;
            $constantsRegex = $this->zasConfig->nameConventionsRegex->constantsClass;
          

            if(preg_match("/$abRegex/", $actualName)){
                return $this->makeAbstractClass($className, $parentClassName, $impInterfaces, $useTraits, $force);
                ZasHelper::log("making abstract class specified");
            }
            else if (preg_match("/$constantsRegex/", $actualName)){
                return $this->makeConstClass($className, $parentClassName);
            }
            else{
                return $this->makeClass($className, $parentClassName, $impInterfaces, $useTraits, $force);
            }
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
            ZasHelper::log("Making Interface: $interfaceName");

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
            if($madeFile->exists)   ZasHelper::log("$actualName Interface already exists. Use --f in your command to overwrite it.");

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
            ZasHelper::log("Making Trait: $traitName");
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
            if($madeFile->exists)   ZasHelper::log("$actualName Trait already exists. Use --f in your command to overwrite it.");

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