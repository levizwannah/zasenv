<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class Updater extends AbstractCommandExecutor  {

        # use traits
        #ut#

        /**
         * Adds a list of function to the file given that it is a class
         * @param array $functions
         * @param mixed $filePath
         * 
         * @return [type]
         */
        public function addFunc(array $functions, $filePath){
            if(count($functions) == 0) return;
            
            $fileContents = trim(file_get_contents($filePath));
            $funcStr = "";
            $fmt = new Formatter();

            foreach($functions as $func){
                $funcStr .= $fmt->tabOnEnter("$func{\n".$fmt->tab()."#code...\n}", ZasConstants::FUNC_INDENT_TAB). $fmt->enter();
            }

            preg_match("/\{[\W\w.]*\}/", $fileContents, $match);        
            $foundCnt = $match[count($match)-1];
            $tmp = explode("}", $foundCnt);
            array_pop($tmp);
            $str = implode("}", $tmp) . $funcStr . $fmt->indent("}");
            $fileContents = str_replace($foundCnt, $str, $fileContents);
            file_put_contents($filePath, $fileContents);
        }
    }

?>
            