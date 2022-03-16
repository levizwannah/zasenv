<?php
    namespace Zas;

use Closure;

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

        protected $interfaces = [];
        protected $traits = [];
        protected $parent = null;

        public function __construct(array $changeMap)
        {
            parent::__construct($changeMap, ClassObject::$temPath);

            $this->defaultChangeMap[ClassObject::CN] = "";
            $this->defaultChangeMap[ClassObject::C_VISIBILITY] = "public";
            $this->defaultChangeMap[ClassObject::NS] = "";
            $this->defaultChangeMap[ClassObject::UNS] = "";
            $this->defaultChangeMap[ClassObject::UT] = "";
        }

        
        
        public function makePhpCode(){

        }

        /**
         * converts the interfaces to a formatable
         */
        private function formatContracts(){
            
        }
        
        /**
         * resolveCollision for namespaces used in class such as
         *
         * @param  array $names
         * @return array $resolved - a map containing [names => qualifiedNames]
         */
        private function resolveCollision(array $names){
            $resolved = [];
            $tree = new NsBST();
            foreach($names as $n){
                    $tree->insert($n);
            }

            $tree->getResolvedQNames($resolved);
            return $resolved;
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