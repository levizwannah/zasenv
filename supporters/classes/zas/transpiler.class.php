<?php
    namespace Zas;
    use Zas\AbstractTranspiler;    

    /**
     * Transpiler base class for other transpilers
     */
    class Transpiler extends AbstractTranspiler{
        /**
         * NS - Namespace
         */
        const NS = "[NS]";
        /**
         * UNS - Use namespace 
         */
        const UNS = "#uns#";

        /**
         * UT - use trait
         */
        const UT = "#ut#";

        /**
         * defaultChangeMap
         * Default values for the template optional placeholders [\w+]
         * @var array
         */
        protected $defaultChangeMap = [];

        /**
         * transpile
         * root transpiler
         * @return void
         */
        public function transpile()
        {   $tplCode = $this->templateCode;
            $this->changeMap += $this->defaultChangeMap;
            
            #fix namespace
            if(!empty($this->changeMap[ClassObject::NS])){
                $this->changeMap[ClassObject::NS] = "namespace ".$this->changeMap[ClassObject::NS] . ";";
            }

            foreach($this->changeMap as $key => $value){
                $tplCode = str_replace($key, $value, $tplCode);
            }

            $this->phpCode = $tplCode;
        }
    }

?>