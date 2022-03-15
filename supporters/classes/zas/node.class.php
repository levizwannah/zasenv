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
    }

?>