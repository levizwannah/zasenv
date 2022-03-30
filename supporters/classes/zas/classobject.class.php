<?php
    namespace Zas;


    /**
     * ClassTranspiler
     * Transpiles the code in class.tpl to php code
     */
    class ClassObject extends Transpiler {
        
        const CN = "[CN]";
        const INHERITANCE = "<INHERITANCE>";
        const CONTRACTS = "<CONTRACTS>";
        const C_VISIBILITY = "[C-VISIBILITY]";
        /**
         * temPath
         * The template path for the class.tpl
         * @var string
         */
        public static $temPath;

        /**
         * An array of interfaces implemented by the class
         * @var array
         */
        protected $interfaces = [];

        /**
         * And array of traits used by the class
         * @var array
         */
        protected $traits = [];

        /**
         * The qualified name of the class
         * @var string
         */
        protected $qualifiedName;

        /**
         * The parent qualified class name
         * @var string
         */
        protected $parent;

        /**
         * Use Namsespace string - to be written at the 
         * point where uns is.
         * @var array
         */
        protected $useNsString = [];
        /**
         * Makes a new classObject
         * @param array $changeMap The change map should contain the class name, and the namespace denoted by [CN] => className, [NS] => Zas\Server for example
         */

        # traits
        use NsUtilTrait; 

        public function __construct(array $changeMap)
        {
            parent::__construct($changeMap, ClassObject::$temPath);

            $this->defaultChangeMap[ClassObject::C_VISIBILITY] = "public";
            $this->defaultChangeMap[ClassObject::UNS] = "#uns#";
            $this->defaultChangeMap[ClassObject::UT] = "#ut#";
            $this->defaultChangeMap[ClassObject::INHERITANCE] = "";
            $this->defaultChangeMap[ClassObject::CONTRACTS] = "";

        }

        
        
        /**
         * Converts the template text in class.tpl to a php code.
         * @return string
         */
        public function makePhpCode(){
                if(empty($this->qualifiedName)){
                        echo "ClassObject::Error: No classname defined\n";
                        return "";
                }

                #add parent to namespace
                $this->addParentToNs();

                #handle Inheritance;
                if(!empty($this->parent)){
                        $parentName = $this->getName($this->parent);
                        $this->changeMap[ClassObject::INHERITANCE] = "extends $parentName";
                }

                #handling contracts
                $this->formatContracts();

                #handling traits
                $this->formatTraits();

                #namespace usage
                if(!empty($this->useNsString)){
                        $useNs = implode("\n    ", $this->useNsString) . "\n    ". ClassObject::UNS;
                        $this->changeMap[ClassObject::UNS] = $useNs;
                }
                

                
                #do final transpilation
                $this->transpile();
                
                return $this->phpCode;
        }

        
        /**
         * Checks if the parent class shares a namespace with the class being created so that no use statement is inserted for the parent class.
         * @return void
         */
        protected function addParentToNs(){
                if(empty($this->parent)) return;
                $pns = $this->getNamespaceText($this->homeDir($this->parent));
                $cns = $this->getNamespaceText($this->homeDir($this->qualifiedName));
               
                if ($pns == $cns) return;
                $this->useNsString[] = "use " . $this->getNamespaceText($this->parent) . ";";
        }

        /**
         * converts the traits to a php formatted code
         * @return void
         */
        protected function formatTraits(){
                
                $this->changeMap[ClassObject::UT] = $this->format($this->traits, function(&$list, &$output){
                        
                        $output = array_map(function($value){
                                return "use $value;";
                        }, $list);

                        $output = implode("\n        ", $output) . "\n        ". ClassObject::UT ;
                });

                if($this->changeMap[ClassObject::UT] === ""){
                        $this->changeMap[ClassObject::UT] = ClassObject::UT;   
                }
        }

        /**
         * converts the interfaces to a php formatted code
         */
        protected function formatContracts(){
            $this->changeMap[ClassObject::CONTRACTS] = $this->format($this->interfaces, function(&$list, &$output){
                    $output = "implements ". implode(", ", $list);
            });
        }
        
        /**
         * resolveCollision for namespaces used in class such as
         *
         * @param  array $names
         * @return array $resolved - a map containing [names => qualifiedNames]
         */
        protected function resolveCollision(array $names){

            $resolved = [];
            $tree = new NsBST();
            foreach($names as $n){
                    $tree->insert($n);
            }

            $tree->getResolvedQNames($resolved);
            return $resolved;
        }

        /**
         * @param array $names
         * @param Function $formatter (&$list, &$output)
         * 
         * @return array|string
         */
        protected function format(array $names, $formatter){
            $all = $this->resolveCollision($names);
            if(count($all) < 1){
                    return "";
            }

            $list = [];
            foreach($all as $name => $qualifiedName){
                    $useStmt = "use $qualifiedName;";
                    if($this->getName($qualifiedName) !== $name){
                            # is alias
                            $useStmt = "use $qualifiedName as $name;";
                    }
                    $this->useNsString[] = $useStmt;
                    $list[] = $name;

            }
            $output = "";
            $formatter($list, $output);
            return $output;
        }

        /**
         * Get the value of interfaces
         */ 
        public function getInterfaces()
        {
                return $this->interfaces;
        }

        /**
         * Set the value of interfaces
         *
         * @return  self
         */ 
        public function setInterfaces(array $interfaces)
        {       
                $this->interfaces = $interfaces;

                return $this;
        }

        /**
         * Get the value of traits
         */ 
        public function getTraits()
        {
                return $this->traits;
        }

        /**
         * Set the value of traits
         *
         * @return  self
         */ 
        public function setTraits(array $traits)
        {
                $this->traits = $traits;

                return $this;
        }

        /**
         * Get the value of parent
         */ 
        public function getParent()
        {
                return $this->parent;
        }

        /**
         * Set the value of parent
         *
         * @return  self
         */ 
        public function setParent($parent)
        {
                $this->parent = $parent;

                return $this;
        }

        /**
         * Get the qualified name of the class
         *
         * @return  string
         */ 
        public function getQualifiedName()
        {
                return $this->qualifiedName;
        }

        /**
         * Set the qualified name of the class
         *
         * @param  string  $qualifiedName  The qualified name of the class
         *
         * @return  self
         */ 
        public function setQualifiedName(string $qualifiedName)
        {
                $this->qualifiedName = $qualifiedName;

                return $this;
        }

        /**
         * Get point where uns is.
         *
         * @return  array
         */ 
        public function getUseNsString()
        {
                return $this->useNsString;
        }

        /**
         * Set point where uns is.
         *
         * @param  array  $useNsString  point where uns is.
         *
         * @return  self
         */ 
        public function setUseNsString(array $useNsString)
        {
                $this->useNsString = $useNsString;

                return $this;
        }
    }

?>