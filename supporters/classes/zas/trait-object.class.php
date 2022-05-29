<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class TraitObject extends Transpiler implements CodeMakerInterface {

        /**
         * Constants representing what should be manipulated in the trait.tpl
         */
        const   TN = "[TN]";

        /**
         * Traits that will be used by this trait
         * @var array
         */
        private $traits = [];

        # use traits
        use NsUtilTrait;
        #ut#

        /**
         * Makes a new TraitOjbect - Trait Object
         * @param array $changeMap The change map should contain the trait name, and the namespace denoted by [TN] => TraitName, [NS] => qualifiedNamespace for example
         */
        public function __construct(array $changeMap){
            parent::__construct($changeMap, TraitObject::$temPath);
            $this->defaultChangeMap[TraitObject::UNS] = "#uns#";
        }


        /**
         * Converts the template text in traits.tpl to a php code.
         * @return string
         */
        public function makePhpCode(){
            if(!$this->canMakePhpCode()) return "";

            #format interfaces
            $this->formatTraits();
            $fmt = new Formatter();    
            #namespace usage
            if(!empty($this->useNsString)){
                    $useNs = $fmt->tabOnEnter(implode("\n", $this->useNsString) . "\n". IfcObject::UNS);
                    $this->changeMap[IfcObject::UNS] = $useNs;
            }
            
            #do final transpilation
            $this->transpile();
            
            return $this->phpCode;
        }

        /**
         * converts the traits to a php formatted code
         * @return void
         */
        protected function formatTraits(){
                
            $this->changeMap[TraitObject::UT] = $this->format($this->traits, function(&$list, &$output){
                    $fmt = new Formatter();

                    $output = array_map(function($value){
                            return "use $value;";
                    }, $list);

                    $output = $fmt->tabOnEnter(implode("\n", $output) . "\n". TraitObject::UT, ZasConstants::FUNC_INDENT_TAB);
            });

            if($this->changeMap[TraitObject::UT] === ""){
                    $this->changeMap[TraitObject::UT] = TraitObject::UT;   
            }
        }

        /**
         * Get traits that will be used by this trait
         *
         * @return  array
         */ 
        public function getTraits()
        {
                return $this->traits;
        }

        /**
         * Set traits that will be used by this trait
         *
         * @param  array  $traits  Traits that will be used by this trait
         *
         * @return  self
         */ 
        public function setTraits(array $traits)
        {
                $this->traits = $traits;

                return $this;
        }
    }

?>