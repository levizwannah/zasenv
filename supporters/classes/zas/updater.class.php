<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class Updater extends AbstractCommandExecutor  {

        # use traits
        #ut#

        public function addFunc(array $functions, $filePath){
            $fileContents = trim(file_get_contents($filePath));
            
            foreach($functions as $func){

            }
        }
    }

?>