<?php
    namespace Zas;
        
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
        {
            foreach($this->changeMap as $key => $value){
                $this->templateCode = str_replace($key, $value, $this->templateCode);
            }
        }
    }

?>