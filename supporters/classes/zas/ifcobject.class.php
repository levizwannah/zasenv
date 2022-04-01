<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class IfcObject extends Transpiler  {

        /**
         * Constants defining what should be manipulated in the interface.tpl
         */
        const   IN = "[IN]",
                CONTRACTS = "<EXTENSIONS>";

        /**
         * An array of interfaces implemented by the interface
         * @var array
         */
        protected $interfaces = [];

        # use traits
        use NsUtilTrait;
        #ut#

        /**
         * Makes a new InfOjbect - Interface Object
         * @param array $changeMap The change map should contain the interface name, and the namespace denoted by [IN] => InterfaceName, [NS] => Zas\Server for example
         */
        public function __construct(array $changeMap){
            parent::__construct($changeMap, IfcObject::$temPath);
            $this->defaultChangeMap[IfcObject::UNS] = "#uns#";
            $this->defaultChangeMap[IfcObject::CONTRACTS] = "";
        }

         /**
         * Converts the template text in class.tpl to a php code.
         * @return string
         */
        public function makePhpCode(){
            if(!$this->canMakePhpCode()) return "";

            #format interfaces
            $this->formatContracts();

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
         * converts the interfaces to a php formatted code
         */
        protected function formatContracts(){
            $this->changeMap[IfcObject::CONTRACTS] = $this->format($this->interfaces, function(&$list, &$output){
                    $output = "extends ". implode(", ", $list);
            });
        }

        /**
         * Get an array of interfaces implemented by the interface
         *
         * @return  array
         */ 
        public function getInterfaces()
        {
                return $this->interfaces;
        }

        /**
         * Set an array of interfaces implemented by the interface
         *
         * @param  array  $interfaces  An array of interfaces implemented by the interface
         *
         * @return  self
         */ 
        public function setInterfaces(array $interfaces)
        {
                $this->interfaces = $interfaces;

                return $this;
        }
    }

?>