<?php
    namespace Zas;

    #uns#

    class Node {        
        /**
         * left
         *
         * @var Node
         */
        public $left;        
        /**
         * right
         *
         * @var Node
         */
        public $right;        
        /**
         * namespace
         *
         * @var array
         */
        public $namespace;        
        /**
         * name
         *
         * @var string
         */
        public $name;

        # traits
        use NsUtilTrait;
        #ut#

        
        public function __construct($qualifiedName)
        {
            $this->namespace = [];
            $namespace = $this->getNamespaceText($this->homeDir($qualifiedName));
            $this->namespace[] = $namespace;
            $this->name = $this->getName($qualifiedName);
        }
        
        /**
         * addNs
         * Add a namespace to the node's namespace array.
         * @param  mixed $namespace
         * @return void
         */
        public function addNs($namespace){
            if(array_search($namespace, $this->namespace) !== false) return;
            $this->namespace[] = $namespace;
        }
        
        /**
         * putQUsageName Put qualified usage name in the container.
         *
         * @param  mixed $container The container in which the qualified name should be placed.
         * @return void
         */
        public function putQUsageName(array &$container){
            if(count($this->namespace) == 1){
                $container[] = $this->getQualifiedName($this->namespace[0]);
                return;
            }

            foreach($this->namespace as $index => $ns){
                $splittedNs = preg_split("/\W/", $ns);
                
                # build the prefix to resolve name collision
                $prefix = "";
                foreach($splittedNs as $sns){
                    $prefix .= strtolower($sns[0]);
                }

                $name = $this->name . " as $prefix".$this->name;
                $container[] = $this->getQualifiedName($ns, $name);
            }
            
        }
        
        /**
         * getQualifiedName
         * returns a string to use used in `use qualified name` in namespace usage.
         * @param  mixed $namespace
         * @return void
         */
        private function getQualifiedName($namespace, $name = ""){
            if(empty($name)) $name = $this->name;
            return "$namespace\\". $name;
        }
    }

?>