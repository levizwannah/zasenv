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
            $fileContents = trim(file_get_contents($filePath));
            
            foreach($functions as $func){

            }
        }
    }

?>