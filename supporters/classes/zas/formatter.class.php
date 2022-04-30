<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class Formatter {

        # use traits
        #ut#

        public function __construct(){
            #code...
        }

        /**
         * Returns the number of tabs
         * @param int $numOfTabs
         * 
         * @return [type]
         */
        public function tab(int $numOfTabs = ZasConstants::TAB_DEFAULT){
            return str_repeat("\t", $numOfTabs);
        }

        /**
         * Returns the number of spaces
         * @param int $numOfSpaces
         * 
         * @return [type]
         */
        public function space(int $numOfSpaces = ZasConstants::SPACE_DEFAULT){
            return str_repeat(" ", $numOfSpaces);
        }

        /**
         * Enter on a new line
         * @param int $times
         * 
         * @return [type]
         */
        public function enter(int $times = ZasConstants::ENTER_DEFAULT){
            return str_repeat("\n", $times);
        }

        /**
         * Default indentent
         * @param string $str
         * 
         * @return string
         */
        public function indent(string $str){
            return $this->tab(ZasConstants::INDENT_TAB) . $str;
        }

        /**
         * Default indentation for methods
         * @param string $str
         * 
         * @return string
         */
        public function methodIndent(string $str){
            return $this->tab(ZasConstants::FUNC_INDENT_TAB) . $str;
        }


        /**
         * Put indent after every new line
         * @param string $str
         * 
         * @return string
         */
        public function tabOnEnter( string $str, int $numTabs = ZasConstants::TAB_DEFAULT){
            $strArr = explode("\n", $str);
            $numTabs = $numTabs;
            $newStr = "";
            foreach($strArr as $st){
                $newStr .= $this->enter(1) . $this->tab($numTabs). $st;
            }
            
            return $newStr;
        }

    }

?>