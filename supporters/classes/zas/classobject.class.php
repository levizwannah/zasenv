<?php
    namespace Zas;

    #uns#

    /**
     * ClassTranspiler
     * Transpiles the code in class.tpl to php code
     */
    class ClassObject extends Transpiler {
        
        /**
         * Constants to define what will be manipulated in the class.tpl
         */
        const CN = "[CN]";
        const INHERITANCE = "<INHERITANCE>";
        const CONTRACTS = "<CONTRACTS>";
        const C_VISIBILITY = "[C-VISIBILITY]";
        

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
         * The parent qualified class name
         * @var string
         */
        protected $parent;

        # traits
        #ut#

        /**
         * Makes a new classObject
         * @param array $changeMap The change map should contain the class name, and the namespace denoted by [CN] => className, [NS] => Zas\Server for example
         */
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
                if(!$this->canMakePhpCode()) return "";

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
    }

?>