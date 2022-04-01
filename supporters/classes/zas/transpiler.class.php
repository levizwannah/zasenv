<?php
    namespace Zas;
    use Zas\AbstractTranspiler;    
    #uns#
    
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
         * temPath
         * The template path for the class.tpl
         * @var string
         */
        public static $temPath;

        /**
         * defaultChangeMap
         * Default values for the template optional placeholders [\w+]
         * @var array
         */
        protected $defaultChangeMap = [];

        /**
         * Use Namsespace string - to be written at the 
         * point where uns is.
         * @var array
         */
        protected $useNsString = [];

        /**
         * The qualified name of the container.
         * Qualified names include their namespaces.
         * @var string
         */
        protected $qualifiedName;

        # traits
        use NsUtilTrait; 
        #ut#

        /**
         * transpile
         * root transpiler
         * @return void
         */
        public function transpile()
        {   $tplCode = $this->templateCode;
            
            # overwrite change map
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

        /**
         * Checks if there is a qualified name and that we can 
         * proceed to make the code.
         */
        public function canMakePhpCode(){
            if(empty($this->qualifiedName)){
                echo "ClassObject::Error: No classname given\n";
                return false;
            }

            return true;
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

                # check if we are in the same namespace
                $ns = $this->getNamespaceText($this->homeDir($qualifiedName));
                $cns = $this->getNamespaceText($this->homeDir($this->qualifiedName));
                

                if($cns === $ns) {
                        $list[] = $name;
                        continue;
                }

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