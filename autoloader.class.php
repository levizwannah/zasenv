<?php
    /**
     *  Autoloads classes, interfaces, and traits. It also automatically include the vendor autoloader if the folder is present.
     *  **requires that the zas-config.json be configured properly.**
     */
    class AutoLoader{
        const ZC_TRAIT = "trait";
        const ZC_CLASS = "class";
        const ZC_ACLASS = "abstractClass";
        const ZC_CONST = "constantsClass";
        /**
         * Path Object from the zas-config
         */
        public $path;

        /**
         * Extensions object from the zas-config
         */
        public $extensions;

        /**
         * Names convention regex from the zas-config
         */
        public $convRegex;

        # load the zas-config
        public function __construct()
        {
            #Use the Zas configuration to set the extensions and path
            $zasConfig = file_get_contents(__DIR__. "/zas-config.json");
            $config = json_decode($zasConfig);
            $this->path = $config->path;
            $this->extensions = $config->extensions;
            $this->convRegex = $config->nameConventionsRegex;
        }

        private function getPath($name, $extension, $path){
            #names contain their namespaces attached to them.
            $name = preg_replace("/\\\/", "/", strtolower($name));
            $fullPath = __DIR__."/$path/$name.$extension";
            
            return $fullPath;
        }

        /**
         * @param string $classname The name of the class to load
         */
        public function loadClass(string $className){

            $classPath = $this->path->class;
            $extension = $this->extensions->class;
            $path = $this->getPath($className, $extension, $classPath);

            if(!file_exists($path)) {
                return false;
            }

            require($path);
            return true;
        }

        /**
         * @param string $interfaceName The name of the interface to load
         */
        public function loadInterface(string $interfaceName){
            #interface names contain their namespaces attached to them.
            $iPath = $this->path->interface;
            $extension = $this->extensions->interface;
            $path = $this->getPath($interfaceName, $extension, $iPath);

            if(!file_exists($path)) {
                return false;
            }

            require($path);
            return true;
        }

        /**
         * @param string $traitName The name of the trait to load
         */
        public function loadTrait(string $traitName){
            #traitNames come with their namespaces attached
            $tPath = $this->path->trait;
            $ext = $this->extensions->trait;
            $path = $this->getPath($traitName, $ext, $tPath);

            if(!file_exists($path)){
                return false;
            }

            require($path);
            return true;
        }

        /**
         * @param string $abstractClassName The name of the abstract class to load
         */
        public function loadAbstractClass(string $abstractClassName){
            #abstractClassNames come with their namespaces attached
            $aPath = $this->path->abstractClass;
            $ext = $this->extensions->abstractClass;
            $path = $this->getPath($abstractClassName, $ext, $aPath);

            if(!file_exists($path)){
                return false;
            }

            require($path);
            return true;
        }
        
        /**
         * @param string $constantsName Loads The name of the class constants to load.
         */
        
        public function loadConstantsClass(string $constantsClassName){
            #constantsClassNames come with their namespaces attached
            $aPath = $this->path->constantsClass;
            $ext = $this->extensions->constantsClass;

            $path = $this->getPath($constantsClassName, $ext, $aPath);

            if(!file_exists($path)){
                return false;
            }

            require($path);
            return true;
        } 

        /**
         * @param string $name Name to load. This will include the namespace plus specific name conventions.
         * For example, name Human, load will determine the loader to call using `Type` (Type)?Human(Type)?: Human would result in loading Human class. AbstractHuman for Abstract class, HumanInterface for interface, HumanTrait for trait.
         * 
         */
        public function load($name){
            #check the name to know whether we are loading a class, interface, trait, or abstract class.
            #name include namespace to it.

            $splittedNames = preg_split("/\W/", $name);
            $size = count($splittedNames);

            #are we actually loading something?
            if($size < 1) return;
            $actualName = $splittedNames[$size - 1];

            foreach((array)$this->convRegex as $type => $regex){
               if(!preg_match("/$regex/", $actualName)) continue;

                $nextRegex = preg_replace("/\\\w{1}/", "", $regex);
                $nextRegex = preg_replace("/\W/", "", $nextRegex);
                $actualName = preg_replace("/$nextRegex/", "", $actualName);

                array_pop($splittedNames);
                $name = implode("\\", $splittedNames) . "\\$actualName";
               
               switch($type){
                   case "abstractClass": {
                       return $this->loadAbstractClass($name);
                   }
                   case "trait": {
                       return $this->loadTrait($name);
                   }
                   case "interface": {
                       return $this->loadInterface($name);
                   }
                   case "constantsClass": {
                       return $this->loadConstantsClass($name);
                   }
                   default: {
                        return $this->loadClass($name);
                   }
               }
            }
        }

        /**
         * registers the vendor autoloader if it exists and registers our autoloading function
         */
        public function autoLoad(){
            #setting vendor autoloading
            if(is_dir(__DIR__."/vendor")){
                require (__DIR__."/vendor/autoload.php");
            }   

            spl_autoload_register([$this, "load"]);
            echo "Autoloading...\n";
        }
    }

?>